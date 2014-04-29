#define _WIN32_WINNT 0x0501

#include <stdio.h>
#include <winsock2.h>
#include <ws2tcpip.h>
#include <windows.h>

#define MAXLEN 256
#define MAXCONNS 128

#define DEFAULT_PORT "27015"

typedef struct sockmem
{

  SOCKET clientsock;

  char buf[MAXLEN];

} sockmem;

struct addrinfo *getaddr(const char *const port, struct addrinfo hint);
SOCKET bindsock(struct addrinfo *adr);
int initwsa(short ver, WSADATA *wsadata);
SOCKET makesock(const char *const port);
int lstsock(const char *const port, SOCKET *listensock);
HANDLE scanthreads(HANDLE *threads);
int commthread(SOCKET sock, HANDLE thread);
DWORD WINAPI parsestrm(LPVOID sock);

int main(int argc, char **argv)
{

  SOCKET listensock, clientsock;
  const char *const port = (argc > 1) ? argv[1] : DEFAULT_PORT;
  HANDLE threads[MAXCONNS], curr;


  if(argc < 2)
    printf("Note: usage is \"%s [port]\"\nUsing default port %s\n",argv[0],port);


  while(lstsock(port,&listensock) == SOCKET_ERROR)
    {
      closesocket(listensock);

      printf("Retrying... \n");
    }
  

  while(1)
    {

      while(!(curr = scanthreads(threads)))
	{
	  printf("Maximum number of connections reached.\nWaiting for available socket.\n");
	  Sleep(1000);
	}

      printf("Waiting for incoming connection... \n");

      if((clientsock = accept(listensock,NULL,NULL)) == INVALID_SOCKET)
	  printf("Error accepting connection (code %d).\nRetrying...\n",WSAGetLastError());
      else
	  commthread(clientsock,curr);
      
    }

  closesocket(listensock);
  WSACleanup();

  return 0;

}

SOCKET makesock(const char *const port)
{

  WSADATA wsadata;
  struct addrinfo *adr;
  SOCKET listensock = ~0;
  

  if(!initwsa((WORD)0x202,&wsadata))
    {
      if(adr = getaddr(port,(struct addrinfo){AI_PASSIVE,AF_INET,SOCK_STREAM,IPPROTO_TCP}))
	{
	  listensock = bindsock(adr);
	  freeaddrinfo(adr);
	}
      else
	WSACleanup();
    }

  return listensock;

}

struct addrinfo *getaddr(const char *const port, struct addrinfo hint)
{

  struct addrinfo *res = NULL;
  int err;


  printf("Getting address info... ");


  if(err = getaddrinfo(NULL,port,&hint,&res))
      printf("Error getting address info (code %d)\n",err);
  else
    printf("done.\n");


  return res;

}

SOCKET bindsock(struct addrinfo *adr)
{

  SOCKET listensock = ~0;


  printf("Connecting to socket... ");
  

  for(; adr; adr = adr->ai_next)
    if((listensock = socket(adr->ai_family,adr->ai_socktype,adr->ai_protocol)) != INVALID_SOCKET)
      if(!bind(listensock,adr->ai_addr,adr->ai_addrlen))
	{
	  printf("done.\n");
	  return listensock;
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

int lstsock(const char *const port, SOCKET *listensock)
{

  int err;


  while((*listensock = makesock(port)) == INVALID_SOCKET)
    printf("Retrying... \n");


  printf("Creating socket listener... ");


  if(!(err = listen(*listensock,SOMAXCONN)))
    printf("done.\n");
  else
    printf("Error creating socket listener (code %d)\n",WSAGetLastError());


  return err;

}

int commthread(SOCKET sock, HANDLE thread)
{

  sockmem *thrdheap = calloc(sizeof(sockmem),1);

  thrdheap->clientsock = sock;

  thread = CreateThread(NULL,0,parsestrm,(void *)thrdheap,0,NULL);

  return 0;

}

DWORD WINAPI parsestrm(LPVOID thrdheap)
{

  int err;
  sockmem *spc = (sockmem *) thrdheap;


  printf("[Socket %d] Receiving data...\n",spc->clientsock);


  while((err = recv(spc->clientsock,spc->buf,MAXLEN,0)) > 0)
      printf("[Socket %d] Received: %s\n",spc->clientsock,spc->buf);
 

  if(err == SOCKET_ERROR)
    printf("[Socket %d] Error receiving data (code %d)\n",spc->clientsock,WSAGetLastError());
  else
    printf("[Socket %d] Client has closed connection.\n",spc->clientsock);


  closesocket(spc->clientsock);

  free(thrdheap);


  return 0;

}

HANDLE scanthreads(HANDLE *threads)
{

  int i;
  DWORD stat;


  for(i = 0; i < MAXCONNS; ++i)
    {

      if(!threads[i])
	return threads[i];
      
      GetExitCodeThread(threads[i],&stat);

      if(stat != STILL_ACTIVE)
	return threads[i];

    }

  return NULL;

}
