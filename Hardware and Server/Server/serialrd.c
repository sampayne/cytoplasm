#define _WIN32_WINNT 0x0501

#include <stdio.h>
#include <stdlib.h>
#include <windows.h>

#include "errhandle.h"

#define MAXLEN 128

#define DEFAULT_COM "COM7"

#define GENRW (GENERIC_READ | GENERIC_WRITE)
#define FULLSHR 0x3

#define INIT_DELAY 2500

struct portdata
{

  const char *const portid;

  HANDLE port;

  DCB dcb;

  COMMTIMEOUTS ctms;

};

int openport(struct portdata *info);

int main(int argc, char **argv)
{

  struct portdata serport = {argc > 1 ? argv[1] : DEFAULT_COM};

  char buf[MAXLEN] = "";
  DWORD numrec, totread = 0, j;
  int len = 0;
  char *resp = ".";


  if(argc < 2)
    printf("Note: Usage is \"%s [port name]\"\nUsing default port %s\n",argv[0],serport.portid);


  if(openport(&serport))
    {
      printf("Error opening port (code %d)\n",GetLastError());
      return 1;
    }
  else
    {
      printf("Waiting for serial device to initialise... ");
      Sleep(INIT_DELAY);
      printf("done\n");
    }

  printf("Establishing contact... ");
  if(!WriteFile(serport.port,resp,1,&j,NULL)) printf("write failed, code %d\n",GetLastError());
  printf("done.\n");
  while(totread < MAXLEN)
    {
      
      char cbuf[MAXLEN] = "";
      int i;
      printf("Awaiting response... ");
      if(!ReadFile(serport.port,cbuf,MAXLEN,&numrec,NULL))
	printf("failed, code %d\n",GetLastError());
      printf("done.\n");
      totread += numrec;
      if(totread >= MAXLEN) break;
       if(numrec){
	len += sprintf(buf + len,"%s",cbuf);printf("Received message \"%s\"\n",cbuf);
	WriteFile(serport.port,resp,1,&j,NULL);}
      

    } 

  printf("\n%s\n%d\n",buf,totread);

  return 0;

}

int openport(struct portdata *info)
{

  printf("Opening communications port... ");


  info->port = CreateFile(info->portid,GENRW,FULLSHR,NULL,OPEN_EXISTING,0x80,NULL);


  if(info->port == INVALID_HANDLE_VALUE)
    return serialerr(PORT_OPEN_ERROR,info);   

  
  printf("done.\nConfiguring communications port... ");
  

  if(!GetCommState(info->port,&info->dcb))
    return serialerr(DCB_GET_ERROR,info);
  else
    {
      info->dcb.BaudRate=CBR_9600;
      
      if(!SetCommState(info->port,&info->dcb))
	return serialerr(DCB_SET_ERROR,info);
      else
	{	  
	  ++info->ctms.ReadIntervalTimeout;
	  
	  if(!SetCommTimeouts(info->port,&info->ctms))
	    return serialerr(TIMEOUT_SET_ERROR,info);
	}
    }

  printf("done.\n");

  return 0;

}
