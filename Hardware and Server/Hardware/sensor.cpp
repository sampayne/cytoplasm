#include <SoftwareSerial.h>
#include "BGLib.h"


#define LED_PIN 13

#define GATT_HANDLE_C_RX_DATA 17
#define GATT_HANDLE_C_TX_DATA 20


#define CY_COMMAND_READ_SUCCESS 0
#define CY_COMMAND_READ_ERROR 1
#define CY_COMMAND_INVALID 2

#define CY_SENSOR_ID 00002

#define MAXREADINGS 9

#define STATE_IDLE 0
#define STATE_ADVERTISE 1
#define STATE_READING 2
#define STATE_CONNECTABLE 3
#define STATE_CONNECTED 4

struct readings
{
  
  uint8 num, params;
  
  uint8 hdr[20];
  
  int16 vals[1];
  
};


SoftwareSerial bleSerialPort(3,4);

BGLib ble112((HardwareSerial *)&bleSerialPort,0,1);

unsigned long timeout = 0;

struct readings *data = NULL;

uint8 state;

boolean writing = false;

void setup()
{

  initble();

  setstate(STATE_IDLE);
  

  Serial.begin(38400);

    
  data = initreadings(3);
  

  bleSerialPort.begin(38400);
  
  
  while(state != STATE_ADVERTISE)
  {
    delay(500);
    ble112.ble_cmd_system_hello();
    ble112.checkActivity(1000);
  }

}

int16 v[3] = {0};

uint32_t lastm = 0;

void loop()
{
  
  while(state == STATE_ADVERTISE) ble112.checkActivity();
  
  while(state != STATE_ADVERTISE)
  {
     
    ble112.checkActivity();
   
    if(millis() - timeout > 10000)
      advertise(NULL);
   
    if(data->num < MAXREADINGS && (millis() - lastm > 1000))
    { 
      setstate(STATE_READING);
    
   
      addreading(data,&v[0]);
     
       
      setstate(STATE_CONNECTABLE); 
       
      lastm = millis();
    }
     
  }
   
  ble112.checkActivity();

}


void blebusy()
{
  
  digitalWrite(LED_PIN,HIGH);
  
}

void bleidle()
{
  
  digitalWrite(LED_PIN,LOW);
  
}

void usecpause()
{
  
  delayMicroseconds(1000);
  
}

void bootrt(const ble_msg_system_boot_evt_t *msg)
{
  
  advertise(NULL);
  
}

void syshl(const ble_msg_system_hello_rsp_t *msg)
{
 
  advertise(NULL); 
  
}

void advertise(const struct ble_msg_connection_disconnected_evt_t *msg)
{
   
  timeout = millis();
  
  setstate(STATE_ADVERTISE);
  
    
  ble112.ble_cmd_gap_set_adv_parameters(320,480,7);
  ble112.checkActivity(1000);



  uint8 adv_data[] = {0x02,BGLIB_GAP_AD_TYPE_FLAGS,BGLIB_GAP_AD_FLAG_GENERAL_DISCOVERABLE |
                      BGLIB_GAP_AD_FLAG_BREDR_NOT_SUPPORTED,0x11,
                      BGLIB_GAP_AD_TYPE_SERVICES_128BIT_ALL,0xE4,0xBa,0x94,0xC3,
                      0xC9,0xB7,0xCD,0xB0,0x9B,0x48,0x7A,0x43,0x8A,0xE5,0x5A,0x19};

  ble112.ble_cmd_gap_set_adv_data(0,0x15,adv_data);
  ble112.checkActivity(1000);



  uint8 sr_data[18] = {18,BGLIB_GAP_AD_TYPE_LOCALNAME_COMPLETE,
                       '$','C','Y','$','S','N','-','A','D','V',':'};
                       
  addid(&sr_data[13]);

  ble112.ble_cmd_gap_set_adv_data(1,0x15,sr_data);
  ble112.checkActivity(1000);
    
    

  ble112.ble_cmd_gap_set_mode(BGLIB_GAP_USER_DATA,BGLIB_GAP_DIRECTED_CONNECTABLE);
  ble112.checkActivity(1000);
  
  
}

uint8 *addid(uint8 *cmd)
{
    
    uint8 id[5] = {0x30,0x30,0x30,0x30,0x30};
    
    inttostr(&id[4],CY_SENSOR_ID);
 
    return (uint8 *)memcpy(cmd,&id[0],5);

}

const uint8 *checkcy(const uint8array *dat)
{
  
  for(uint8 i = 0; i < dat->len - 4; ++i)
    if(*((uint32_t *)&dat->data[i]) == 0x24594324)
      return &dat->data[i+4];
   
  return NULL;
  
}

struct readings *initreadings(uint8 params)
{
  
  struct readings *rd = (struct readings *)calloc(1,sizeof(struct readings) +
                                                  (sizeof(uint16) * (params * MAXREADINGS)));


  char hdr[] = {'$','C','Y','$','S','N','-','H','D','R',':',0,0,0,0,0,',',0,',',0x30};
  
  addid((uint8 *)&hdr[11]);
  hdr[17] = params + 0x30;
  
  memcpy(&rd->hdr[0],&hdr[0],20);
  

  rd->num = 0;
  rd->params = params;
  
  
  return rd;
  
}

int16 *addreading(struct readings *rd, int16 *dat)
{
  
  if(rd->num < MAXREADINGS)
  {
    
    int16 *curr = &rd->vals[rd->num * rd->params];
    
    for(uint8 i = 0; i < rd->params; ++i)
      *curr++ = *dat++;
  
    ++rd->num;
    
    return curr;
    
  }
  else
    return NULL;
    
}


void senddata(const uint16 conn)
{
    
  for(uint8 i = 0; i < data->num; ++i)
  {
    
    uint8 buf[20] = {0}, *lst, len;
    
    lst = datstr(data,&buf[0],data->num-1);
    len = ++lst - &buf[0];

    ble112.ble_cmd_attclient_attribute_write(conn,GATT_HANDLE_C_RX_DATA,len,&buf[0]);   
    ble112.checkActivity(1000);
    
    delay(500);
    
  }
  
  data->num = 0;
  
  uint8 endp[] = {'$','C','Y','$','S','N','-','E','N','D',':','0'};
  
  ble112.ble_cmd_attclient_attribute_write(conn,GATT_HANDLE_C_RX_DATA,12,&endp[0]); 
  ble112.checkActivity(1000);
  
}

void sendreadings(const struct ble_msg_connection_status_evt_t *msg)
{
  
  if(data->num)
  {
    
    setstate(STATE_CONNECTED);
    
    data->hdr[20] += data->num + 1;
    
    ble112.ble_cmd_attclient_attribute_write(msg->connection,GATT_HANDLE_C_RX_DATA,20,&data->hdr[0]);   
    delay(300);
    ble112.checkActivity(1000);
    
  }
  else
    senderr(msg);
  
}

void regdev(const struct ble_msg_connection_status_evt_t *msg)
{
  
  uint8 d[16] = {'$','C','Y','$','S','N','-','R','E','G',':'};
  addid(&d[11]);

  ble112.ble_cmd_attclient_attribute_write(msg->connection,GATT_HANDLE_C_RX_DATA,16,&d[0]);   
  ble112.checkActivity(1000);
  
}

void checkresp(const struct ble_msg_attributes_value_evt_t *msg)
{
 
  const uint8 *cmd;
    
  if(cmd = checkcy(&msg->value))
  {  
    
    while(*cmd++ != ':') ;
        
    switch(*cmd - 0x30)
    {
    
      case CY_COMMAND_READ_ERROR:
      case CY_COMMAND_INVALID:
                                    resumerd(NULL);
                                    break;
      
      case CY_COMMAND_READ_SUCCESS:
                                    senddata(msg->connection);
                                    break;
    }
    
  }
  
}

void checkreg(const struct ble_msg_attributes_value_evt_t *msg)
{
  
  const uint8 *cmd;
  
  if(cmd = checkcy(&msg->value))
  {    
    
    while(*cmd++ != ':') ;
        
    if(*cmd - 0x30)
      advertise(NULL);
    else
      setstate(STATE_CONNECTABLE);
      
  }
  
}

void senderr(const struct ble_msg_connection_status_evt_t *msg)
{
  
  uint8 cmd[] = {'$','C','Y','$','S','N','-','A','D','V',':','1'};
  
  ble112.ble_cmd_attclient_attribute_write(msg->connection,GATT_HANDLE_C_RX_DATA,12,&cmd[0]);
  ble112.checkActivity(1000);
  
}

uint8 *inttostr(uint8 *str, int16 val)
{
  
  for(int16 v = val; v; v /= 10)
    *str-- = 0x30 + v % 10;

  if(val < 1)
    *str-- = !val ? 0x30 : '-';
  
  return ++str;
  
}

uint8 *datstr(struct readings *rd, uint8 *buf, uint8 num)
{
  
  for(uint8 i = 0; i < rd->params; ++i)
  {
    
    uint8 val[8] = {0}, *pos;
    val[6] = ',';
      
    pos = inttostr(&val[5],rd->vals[num * rd->params + i]);
    
    while(*buf++ = *pos++) ;
    
    --buf;
    
  }
  
  return buf;
  
}

void resumerd(const struct ble_msg_connection_disconnected_evt_t *msg)
{
  
  ble112.ble_cmd_gap_set_mode(BGLIB_GAP_USER_DATA,BGLIB_GAP_DIRECTED_CONNECTABLE);
  ble112.checkActivity(1000);

  timeout = millis();
  setstate(STATE_CONNECTABLE); 
  
}

void initble()
{
  
    pinMode(LED_PIN,OUTPUT);
    digitalWrite(LED_PIN,LOW);
    

    ble112.onBusy = blebusy;
    ble112.onIdle = bleidle;
    
    ble112.onBeforeTXCommand = usecpause;
    ble112.onTXCommandComplete = usecpause;
    
    ble112.ble_evt_system_boot = bootrt;
    ble112.ble_rsp_system_hello = syshl;

}

void setstate(uint8 s)
{

  switch(state = s)
  {
    
    case STATE_IDLE:
                             ble112.ble_evt_connection_status = sendreadings;
                             ble112.ble_evt_connection_disconnected = resumerd;
                             ble112.ble_evt_attributes_value = 0;
                             
                             break;
    
    
    case STATE_READING:
                             ble112.ble_evt_connection_status = senderr;
                             ble112.ble_evt_connection_disconnected = resumerd;
                             ble112.ble_evt_attributes_value = 0;
                             
                             break;
  
    case STATE_ADVERTISE:
                             ble112.ble_evt_connection_status = regdev;
                             ble112.ble_evt_connection_disconnected = advertise;
                             ble112.ble_evt_attributes_value = checkreg;
                             
                             break;
    
    case STATE_CONNECTABLE:  
                             ble112.ble_evt_connection_status = sendreadings;
                             ble112.ble_evt_connection_disconnected = resumerd;
                             ble112.ble_evt_attributes_value = 0;
                             
                             break;
    
    case STATE_CONNECTED:
                             ble112.ble_evt_connection_status = 0;
                             ble112.ble_evt_connection_disconnected = resumerd;
                             ble112.ble_evt_attributes_value = checkresp;
                             
                             break;
  } 
  
}
