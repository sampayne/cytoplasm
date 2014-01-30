#include <Time.h>

#include <GSM.h>


GSM gsmAccess; 
GPRS gprsAccess; 
GSMClient client; 

int randomise(int);

void setup()
{
  Serial.begin(9600);
}

void loop()
{

  Serial.print("Connecting to GSM network... ");

  while(gsmAccess.begin() != GSM_READY)
  {
    Serial.println("");
    Serial.print("Failed, retrying... ");
    delay(1000);
  }

  Serial.println("Done.");

  Serial.print("Attaching to GPRS... ");

  while(gprsAccess.attachGPRS("bluevia.movistar.es","","") != GPRS_READY)
  {
    Serial.println("");
    Serial.print("Failed, retrying...");
    delay(1000);
  }

  Serial.println("Done.");

  Serial.print("Synchronising internal clock with server... ");

  Serial.print("Connecting... ");

  while (!client.connect("gsmtestellerysmith.azurewebsites.net",80))
  {
    Serial.println("");
    Serial.print("Failed, retrying... ");
    delay(1000);
  }

  String getreq = "GET /index.php?";
  getreq+=rand();
  getreq+=" HTTP/1.1";
  client.println(getreq);
  client.println("Host: gsmtestellerysmith.azurewebsites.net");
  client.println("Connection: close");
  client.println();

  while(!client.available()) ;
  char datestr[10];

  while(client.available())
    if(client.read() == '%')
      for(int i = 0; client.available(); i++)
        datestr[i] = client.read();

  long dateval = atol(datestr);
  
  Serial.println("Done.");

  client.stop();
  
  setTime(dateval);

  int rate = 72;
  double temp = 37.0;

  for(;;)
  {

    Serial.print("Connecting... ");

    while (!client.connect("gsmtestellerysmith.azurewebsites.net",80))
    {
      Serial.println("");
      Serial.print("Failed, retrying...");
      delay(1000);
  Serial.print("Connecting to GSM network... ");

  while(gsmAccess.begin() != GSM_READY)
  {
    Serial.println("");
    Serial.print("Failed, retrying... ");
    delay(1000);
  }

  Serial.println("Done.");

  Serial.print("Attaching to GPRS... ");

  while(gprsAccess.attachGPRS("bluevia.movistar.es","","") != GPRS_READY)
  {
    Serial.println("");
    Serial.print("Failed, retrying...");
    delay(1000);
  }

  Serial.println("Done.");
    }

    Serial.println("Connected.");

    Serial.print("Sending... ");

    String data;

    data += "name=Ellery%20Smith";
    data += "&rate=";
    data += rate;
    data += "&temp=";
    char buf1[16];
    dtostrf(temp,4,2,buf1);
    data += buf1;
    data += "&date=";
    data += now();
    data += "&submit=Submit";
    Serial.println(data);

    rate = rndhr(rate);
    temp = rndtmp(temp);

    client.println("POST /index.php HTTP/1.1");
    client.println("Host: gsmtestellerysmith.azurewebsites.net");
    client.println("Content-Type: application/x-www-form-urlencoded");
    client.println("Connection: close");
    client.print("Content-Length: ");
    client.println(data.length());
    client.println();
    client.println(data);
    client.println();

    client.stop();

    Serial.println("Sent.");

    Serial.println("Disconnected.");

    delay(3000);

  }
}

int rndhr(int val)
{

  int newval = val + ((rand() % 7) - 3);

  if(newval > 120) newval -= 4;
  else if(newval < 55) newval += 4;

  return newval;

}

double rndtmp(double val)
{

  double newval = val + (double)rand()/(RAND_MAX + 1) + 0.5;

  if(newval > 39) newval -= 0.6;
  else if(newval < 36) newval += 0.6;

  return newval;

}

