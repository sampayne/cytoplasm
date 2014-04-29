
#include <SoftwareSerial.h>
#include "BGLib.h"


struct sensor
{
  
  uint8 addr[6];
  
  uint8 id;
  
  struct sensor *next;
  
};

struct connections
{
  
  uint8 num;
  
  struct sensor *first;
  
};

SoftwareSerial bleSerialPort(3,4);
BGLib ble112((HardwareSerial *)&bleSerialPort, 0, 1);


#define LED_PIN 13


#define GATT_HANDLE_C_RX_DATA   17
#define GATT_HANDLE_C_TX_DATA   20


#define CY_COMMAND_READ_SUCCESS 0
#define CY_COMMAND_READ_ERROR 1
#define CY_COMMAND_INVALID 2


#define CY_COMMAND_UNKNOWN 3
#define CY_COMMAND_ADVERTISE 4
#define CY_COMMAND_REGISTER 5
#define CY_COMMAND_HEADER 6
#define CY_COMMAND_ENDPR 7


#define STATE_IDLE 0
#define STATE_SCANNING 1
#define STATE_CONNECTING 2
#define STATE_DATA_MODE 3


uint8 state;

struct connections conns = {0,NULL};

uint32_t lastm = 0;

struct sensor *curr = NULL;


void setup()
{
 
    initble();
    
    setstate(STATE_IDLE);
    
    Serial.begin(38400);
    

    bleSerialPort.begin(38400);
    
    
    while(state == STATE_IDLE)
    {
      delay(500);
      ble112.ble_cmd_system_hello();
      ble112.checkActivity(1000);
    }
    
}

void loop()
{

  Serial.println("$CY$TR-REG:00001");
  
  setstate(STATE_IDLE);

  while(1)
  {  
    
    ble112.checkActivity();
    
    if(millis() - lastm > 5000 && state == STATE_IDLE)
    { 
      
      ble112.ble_cmd_gap_discover(BGLIB_GAP_DISCOVER_OBSERVATION);
      ble112.checkActivity(1000);

      lastm = millis();
        
    }
      
    else if(millis() - lastm > 500 && state == STATE_SCANNING)
    {
            
      ble112.ble_cmd_gap_end_procedure();
      ble112.checkActivity(1000); 
      ble112.resync();
       
      lastm = millis();
      
    }
    

    if((curr = conns.first) && state == STATE_IDLE)
    {
      
      setstate(STATE_CONNECTING);
      
      directconn(curr);
      
      uint32_t timeout = millis();
    
      while(state != STATE_IDLE && (millis() - timeout < 4000)) ble112.checkActivity();
      
    }
    
  }
 
}

void directconn(struct sensor *conn)
{        
           Serial.println(conn->id);
  uint8 pld[15] = {0,0,0,0,0,0,0,0x50,0,0x80,0xC,0xE8,0x3,0,0};
    
  memcpy(&pld[0],&conn->addr,sizeof(bd_addr));
  
  ble112.sendCommand(15,(uint8)0x6,(uint8)0x3,&pld);
  ble112.checkActivity(1000);
  ble112.resync();
  
}


void ledbusy()
{
  digitalWrite(LED_PIN,HIGH);
}

void ledidle()
{
  digitalWrite(LED_PIN,LOW);
}

void usecpause()
{
  delayMicroseconds(1000);
}

void bootproc(const ble_msg_system_boot_evt_t *msg)
{
  
  setstate(STATE_SCANNING);
  
  ble112.ble_cmd_gap_set_scan_parameters(0xC8,0xC8,1);
  ble112.checkActivity(1000);
 
}

void syshl(const ble_msg_system_hello_rsp_t *msg)
{
 
  setstate(STATE_SCANNING); 
  
  ble112.ble_cmd_gap_set_scan_parameters(0xC8,0xC8,1);
  ble112.checkActivity(1000);
  
}
void advresp(const ble_msg_gap_scan_response_evt_t *msg)
{
  
  if(checkcy(&msg->data))
  {
    
    uint8 nid;
       

    if(checkcmd(&msg->data) != CY_COMMAND_ADVERTISE)
      return;
    else if(!(nid = parseid(&msg->data)))
      return;

       
    if(conns.first)
      for(struct sensor *s = conns.first; s; s = s->next)
        if(s->id == nid)
          return;

       
    struct sensor *nw = (struct sensor *)malloc(sizeof(struct sensor));
  
    nw->next = NULL;
    nw->id = nid;
     
    memcpy(nw->addr,msg->sender.addr,sizeof(nw->addr));
     
     
    if(!conns.first)
      conns.first = nw;
    else
    {
        
      struct sensor *s = conns.first;
      
      for(; s->next; s = s->next)
        ;
        
      s->next = nw;
        
    }
      
    ++conns.num;

    return;
          
  }
   
}

void checkconn(const struct ble_msg_gap_connect_direct_rsp_t *msg)
{
  
  if(msg->result)
    nextsen(NULL);

}

void endproc(const struct ble_msg_gap_end_procedure_rsp_t *msg)
{
 
  if(!msg->result) 
    setstate(STATE_IDLE);
    
  else if(msg->result == 0x0181)
    setstate(STATE_IDLE);
    
  else
    ble112.ble_cmd_gap_end_procedure();
    
}

void readcmd(const struct ble_msg_attributes_value_evt_t *msg)
{

  if(checkcy(&msg->value))
  {
      
    for(int i = 0; i < msg->value.len; ++i)
      Serial.print((char)msg->value.data[i]);
  
    Serial.print("\n");
    
    
    switch(checkcmd(&msg->value))
    {
      
      case CY_COMMAND_REGISTER:
                                  sendresp(msg->connection,CY_COMMAND_READ_SUCCESS);
                                  sndisconn(msg->connection);
                                
                                  break;
    
      case CY_COMMAND_HEADER:
                                  setstate(STATE_DATA_MODE);
                                  sendresp(msg->connection,CY_COMMAND_READ_SUCCESS);
 
                                  break;
                                
      case CY_COMMAND_ENDPR:
                                  setstate(STATE_CONNECTING);
                                  sndisconn(msg->connection);
                                
                                  break;
                                
      case CY_COMMAND_UNKNOWN:
                                  sendresp(msg->connection,CY_COMMAND_INVALID);
                                  sndisconn(msg->connection);
                                
                                  break;
      
      
      case CY_COMMAND_ADVERTISE:
      case CY_COMMAND_READ_ERROR:
                                  sendresp(msg->connection,CY_COMMAND_READ_ERROR);
                                  sndisconn(msg->connection);
                                
                                  break;
                                
    }
    
  }
  else
    sndisconn(msg->connection);
 
}

void scanstart(const struct ble_msg_gap_discover_rsp_t *msg)
{
  
  if(!msg->result)
    setstate(STATE_SCANNING);
  
}

void nextsen(const struct ble_msg_connection_disconnected_evt_t *msg)
{

  if(curr = curr->next)
    directconn(curr);
  else
    setstate(STATE_IDLE);
    
}

const uint8 *checkcy(const uint8array *dat)
{
  
  for(uint8 i = 0; i < dat->len - 4; ++i)
    if(*((uint32_t *)&dat->data[i]) == 0x24594324)
      return &dat->data[i+4];
 
  return NULL;
  
}

void sendresp(const uint8 conn, uint8 type)
{
  
  uint8 rsp[] = {'$','C','Y','$','T','R','-','R','S','P',':',type + 0x30};
  
  ble112.ble_cmd_attclient_attribute_write(conn,GATT_HANDLE_C_RX_DATA,12,&rsp[0]); 
  ble112.checkActivity(1000);
  
}

uint8 parseid(const uint8array *dat)
{
  
  char idstr[6];
  uint8 i;
  
  for(i = 0; i < dat->len - 5;)
    if(dat->data[i++] == ':')
      break;   
       
  for(uint8 j = i; j < 5; ++j)
    if(dat->data[j] < 30 || dat->data[j] > 39)
      return 0;
     
       
   memcpy(&idstr[0],&dat->data[i],sizeof(idstr)-1);
   idstr[5] = '\0';
   
   return (uint8)atoi(idstr);
  
}

void initble()
{
  
    pinMode(LED_PIN,OUTPUT);
    digitalWrite(LED_PIN,LOW);
    
    ble112.onBusy = ledbusy;
    ble112.onIdle = ledidle;
    
    ble112.onBeforeTXCommand = usecpause;
    ble112.onTXCommandComplete = usecpause;

    ble112.ble_evt_system_boot = bootproc;
    ble112.ble_rsp_system_hello = syshl;
    
    ble112.ble_rsp_gap_discover = scanstart;
    ble112.ble_rsp_gap_end_procedure = endproc;
    
}

struct sensor *getconnbyid(uint8 cid)
{
 
  for(struct sensor *s = conns.first; s; s = s->next)
    if(s->id == cid)
      return s;
  
}

void drconndc(const struct ble_msg_gap_connect_direct_rsp_t *msg)
{
  
  if(!msg->result)
    sndisconn(msg->connection_handle); 
    
}

uint8 checkcmd(const uint8array *dat)
{
  
  uint32_t cmd = 0;
  
  
  for(uint8 i = 0; i < dat->len - 4; ++i)
    if(dat->data[i] == '-')
    {
      cmd = *((uint32_t *)&dat->data[++i]);
      break;
    }
  

  if((cmd >> 24) & 0x3A)
  {

    switch(cmd)
    {
      
      case 0x3A564441:
                       return CY_COMMAND_ADVERTISE;
     
      case 0x3A474552:
                       return CY_COMMAND_REGISTER;
     
      case 0x3A524448:
                       return CY_COMMAND_HEADER;
     
      case 0x3A444E45:
                       return CY_COMMAND_ENDPR;
  
      default:
                       return CY_COMMAND_UNKNOWN;
                       
    }
    
  }
  else
    return CY_COMMAND_READ_ERROR;
 
}

void sndisconn(const uint8 conn)
{
    
  ble112.ble_cmd_connection_disconnect(conn);
  ble112.checkActivity(1000);
  
}

void cstatdc(const struct ble_msg_connection_status_evt_t *msg)
{
 
  sndisconn(msg->connection); 
  
}

void cmdmode(const struct ble_msg_connection_disconnected_evt_t *msg)
{
 
  setstate(STATE_CONNECTING);
 
  nextsen(msg);
  
}

void readdata(const struct ble_msg_attributes_value_evt_t *msg)
{

  if(checkcy(&msg->value))
    readcmd(msg);
  else
  {
    for(uint8 i = 0; i < msg->value.len; ++i)
      Serial.print((char)msg->value.data[i]);
      
    Serial.print("\n");
  }
  
}

void setstate(uint8 s)
{    

  switch(state = s)
  {
    
    case STATE_IDLE:
                           ble112.ble_evt_connection_status = cstatdc;
                           ble112.ble_rsp_gap_connect_direct = drconndc;
                           ble112.ble_evt_attributes_value = 0;
                           ble112.ble_evt_connection_disconnected = 0;
                           ble112.ble_evt_gap_scan_response = 0;
    
                           break;
                           
    case STATE_SCANNING:
                           ble112.ble_evt_connection_status = cstatdc;
                           ble112.ble_rsp_gap_connect_direct = drconndc;
                           ble112.ble_evt_attributes_value = 0;
                           ble112.ble_evt_connection_disconnected = 0;
                           ble112.ble_evt_gap_scan_response = advresp;
    
                           break;
                           
    case STATE_CONNECTING:
                           ble112.ble_evt_connection_status = 0;
                           ble112.ble_rsp_gap_connect_direct = checkconn;
                           ble112.ble_evt_attributes_value = readcmd;
                           ble112.ble_evt_connection_disconnected = nextsen;
                           ble112.ble_evt_gap_scan_response = 0;
                           
                           break;
                           
    case STATE_DATA_MODE:
                           ble112.ble_evt_connection_status = 0;
                           ble112.ble_rsp_gap_connect_direct = 0;
                           ble112.ble_evt_attributes_value = readdata;
                           ble112.ble_evt_connection_disconnected = cmdmode;
                           ble112.ble_evt_gap_scan_response = 0;
                           
                           break;

  } 
  
}