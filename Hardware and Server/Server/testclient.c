#define _WIN32_WINNT 0x0501

#include <stdio.h>
#include <winsock2.h>
#include <ws2tcpip.h>
#include <windows.h>

#define MAXLEN 256

struct addrinfo *getaddr(char **info, struct addrinfo hint);
SOCKET connvalidip(struct addrinfo *adr);
int initwsa(short ver, WSADATA *wsadata);
SOCKET makesock(char **argv);
int sendmsg(SOCKET sock, char *msg, int len);

int main(int argc, char **argv)
{

  SOCKET connsock;

  if(argc < 3)
    {
      printf("Error: format is \"%s [ip] [port]\"\n",argv[0]);
      exit(1);
    }

  while((connsock = makesock(argv)) == INVALID_SOCKET)
    printf("Retrying... \n");

  long i;
  char buf[256];
  // artid uid transmitterid sensorid taxon
  sprintf(buf,"40&16&12&55&test1-subgroup1-subgroup2-test4");
  sendmsg(connsock,buf,strlen(buf));
  Sleep(500);

  for(i = 0; i < 100; ++i)
    {
      sprintf(buf,"2&%Ld&%Ld",i*4,i+45);
      Sleep(500);
	sendmsg(connsock,buf,strlen(buf));

    }


  closesocket(connsock);
  WSACleanup();

  return 0;

}

SOCKET makesock(char **argv)
{

  WSADATA wsadata;
  struct addrinfo *adr;
  SOCKET connsock = ~0;
  

  if(!initwsa((WORD)0x202,&wsadata))
    {
      if(adr = getaddr(argv,(struct addrinfo){0,AF_INET,SOCK_STREAM,IPPROTO_TCP}))
	{
	  connsock = connvalidip(adr);
	  freeaddrinfo(adr);
	}
      else
	WSACleanup();
    }

  return connsock;

}

struct addrinfo *getaddr(char **info, struct addrinfo hint)
{

  struct addrinfo *res = NULL;
  int err;


  printf("Getting address info... ");


  if(err = getaddrinfo(info[1],info[2],&hint,&res))
      printf("Error getting address info (code %d)\n",err);
  else
    printf("done.\n");


  return res;

}

SOCKET connvalidip(struct addrinfo *adr)
{

  SOCKET connsock = ~0;


  printf("Connecting to socket... ");
  

  for(; adr; adr = adr->ai_next)
    if((connsock = socket(adr->ai_family,adr->ai_socktype,adr->ai_protocol)) != INVALID_SOCKET)
      if(!connect(connsock,adr->ai_addr,adr->ai_addrlen))
	{
	  printf("done.\n");
	  return connsock;
	}
  

  printf("Error connecting to socket (code %d)\n",WSAGetLastError());

  return INVALID_SOCKET;
  
}

int initwsa(short ver, WSADATA *wsadata)
{

  int err;
  

  printf("Initialising WSA... ");
  

  if(err = WSAStartup(ver,wsadata))
    {
      printf("Error initialising WSA (Code %d)\n",err);
      return 1;
    }


  printf("done.\n");
  
  return 0;
  
}

int sendmsg(SOCKET connsock, char *msg, int len)
{

  printf("Sending message \"%s\"... ",msg);
  send(connsock,msg,len,0);
  printf("done.\n");
  
  return 1;

}
