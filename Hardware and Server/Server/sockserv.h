#define DEFAULT_PORT "27015"

struct entdat
{

  SQLINTEGER aid,
    uid,
    tid, 
    sid, 
    txn,
    hd;

};

struct sockmem
{

  SOCKET clientsock;

  char buf[MAXLEN];

  struct sqlqry qrydat;

  struct sqlrestbl res;

  struct entdat ent;

  struct sqlconn *sessinfo;

};

struct addrinfo *getaddr(const char *const port, struct addrinfo hint);
SOCKET bindsock(struct addrinfo *adr);
int initwsa(short ver, WSADATA *wsadata);
SOCKET makesock(const char *const port);
int lstsock(const char *const port, SOCKET *listensock);
HANDLE scanthreads(HANDLE *threads);
int commthread(SOCKET sock, HANDLE thread, struct sqlconn *sessinfo);

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

int commthread(SOCKET sock, HANDLE thread, struct sqlconn *sessinfo)
{

  struct sockmem *thrdheap = calloc(sizeof(struct sockmem),1);

  thrdheap->clientsock = sock;

  thrdheap->sessinfo = sessinfo;

  thread = CreateThread(NULL,0,parsestrm,(void *)thrdheap,0,NULL);

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
 
