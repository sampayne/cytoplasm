#define _WIN32_WINNT 0x0501

#include <windows.h> 
#include <winsock2.h>
#include <ws2tcpip.h>
#include <sql.h> 
#include <sqlext.h> 
#include <stdio.h> 
#include <stdlib.h>

DWORD WINAPI parsestrm(LPVOID thrdheap);

#include "sqllib.h"
#include "sockserv.h"

struct sockpool
{

  int sockcount;

  SOCKET conns[MAXCONNS];

  HANDLE sockmtx;

};

struct connmgrdat
{

  SOCKET listensock;

  struct sockpool *pool;

  DWORD thrdid;

  struct sqlconn *sessinfo;

};

struct thrddata
{

  char buf[MAXLEN];

  int active;

  HANDLE hdl, bufmtx;

  DWORD thrdid;

};

struct datamgrdat
{

  struct thrddata **threads;

  struct bufqueue *queue;

  DWORD thrdid;

};

struct bufqueue
{

  HANDLE qmtx;

  struct deviceconn *first;
  struct deviceconn *last;

};

struct sockmgrdat
{

  struct sockpool *pool;

  struct bufqueue *queue;

  DWORD thrdid;

};

struct deviceinfo
{
  
  //sqlinfo
  char tblname[MAXRESSTR];

};

struct deviceconn
{

  WSAOVERLAPPED ovl;

  SOCKET sock;

  struct deviceinfo *info;

  char buf[MAXLEN];

  WSABUF wsabuf;

};


int initsqlenv(struct sqlconn **sessinfo);
char *nextattr(char *str, char *buf);
int parseart(char *buf, struct sqlqry *qrydat, struct sqlrestbl *res);
int newentrytbl(char *name, char *cols, struct sqlqry *qrydat);
int addentry(char *tblname, char *data, struct sqlqry *qrydat);
DWORD WINAPI connmgr(LPVOID lstinfo);
int initsockpool(struct connmgrdat *conn);
int addtosockpool(struct sockpool *pool, SOCKET sock);
SOCKET nextsockconn(struct sockpool *pool);
int initsockmgr(struct sockmgrdat **dat, struct sockpool *pool);
int initdatamgr(struct datamgrdat **dat, struct bufqueue *queue);
int initqueue(struct bufqueue **queue);
DWORD WINAPI datamgrthrd(LPVOID info);
DWORD WINAPI sockmgrthrd(LPVOID info);
DWORD WINAPI workerthrd(LPVOID info);
void CALLBACK readsockstream(DWORD err, DWORD len, LPWSAOVERLAPPED ovr, DWORD flags);

int main(int argc, char **argv)
{

  struct connmgrdat lstinfo = {0};
  const char *const port = (argc > 1) ? argv[1] : DEFAULT_PORT;
  HANDLE mgrthrd;


  if(argc < 2)
    printf("Note: usage is \"%s [port]\"\nUsing default port %s.\n",argv[0],port);


  if(initsqlenv(&lstinfo.sessinfo))
    return 1;


  while(lstsock(port,&lstinfo.listensock) == SOCKET_ERROR)
    {
      closesocket(lstinfo.listensock);

      printf("Retrying... \n");
    }

  mgrthrd = CreateThread(NULL,0,connmgr,(LPVOID)&lstinfo,0,&lstinfo.thrdid);

  WaitForSingleObject(mgrthrd,INFINITE);

  CloseHandle(mgrthrd);
  closesocket(lstinfo.listensock);
  WSACleanup();

  return 0;

}

int initsqlenv(struct sqlconn **sessinfo)
{

  struct sqlconn *new = malloc(sizeof(struct sqlconn));

  char *params[2] = {"(local)","master"};

  printf("Connecting to SQL database.\n");
  
  if(makeconnstr(new->connstr,params,0))  
    if(!dbconn(new))
      {
	*sessinfo = new;
	printf("Connected to SQL database.\n");
	return 0;
      }
  
  printf("Failed to connect to SQL database.\n");
  return 1;

}

DWORD WINAPI parsestrm(LPVOID thrdheap)
{

  int err;
  struct sockmem *spc = (struct sockmem *) thrdheap;
  struct sqlqry qrydat;
  struct sqlrestbl res;
  char tblname[MAXRESSTR];


  prepqry(spc->sessinfo->conn,&qrydat);


  printf("[Socket %d] Receiving data...\n",spc->clientsock);


  if(recv(spc->clientsock,spc->buf,MAXLEN,0) > 0)
    { 
      printf("[Socket %d] Received: %s\n",spc->clientsock,spc->buf);

      nextattr(spc->buf,&tblname[0]);
      
      parseart(spc->buf,&qrydat,&res);
    }


  while((err = recv(spc->clientsock,spc->buf,MAXLEN,0)) > 0)
    {
      spc->buf[err] = '\0';

      printf("[Socket %d] Received: %s\n",spc->clientsock,spc->buf);
      
      addentry(&tblname[0],spc->buf,&qrydat);
    }

  if(err == SOCKET_ERROR)
    printf("[Socket %d] Error receiving data (code %d)\n",spc->clientsock,WSAGetLastError());
  else
    printf("[Socket %d] Client has closed connection.\n",spc->clientsock);


  closesocket(spc->clientsock);

  free(thrdheap);


  return 0;

}

int addentry(char *tblname, char *data, struct sqlqry *qrydat)
{

  char entry[MAXRESSTR];
  int colnum;
  long i;
  double val;
  
  data = nextattr(data,&entry[0]);
  colnum = atoi(entry);

  if(buildstmt(qrydat,QRY_INSERT,tblname,colnum))
    {
      for(i = 0; i < colnum; ++i)
	{
	  data = nextattr(data,&entry[0]);
	  
	  if(i < colnum -1)
	    {
	      val = atof(entry);
	      
	      bindval(qrydat,&val,SQL_C_DOUBLE);
	    }
	  else
	    {
	      i = atol(entry);
	      
	      bindval(qrydat,&i,SQL_C_SLONG);
	    }
	}
      
      if(execqry(qrydat,NULL))
	return 1;
      else
	return 0;
    }
  else
    return 1;

}

char *nextattr(char *str, char *buf)
{

  int i;

  for(i = 0; i < strlen(str); ++i)
    if(str[i] == '&')
      break;
    else
      buf[i] = str[i];

  buf[i++] = '\0';

  return str + i;

}

int parseart(char *buf, struct sqlqry *qrydat, struct sqlrestbl *res)
{

  char name[MAXRESSTR];

  nextattr(buf,&name[0]);


  if(buildstmt(qrydat,QRY_SELECT,"articles",0))
    {
      if(execqry(qrydat,res))
	return 1;
    }
  else
    return 1;


  if(!searchcol(res,"name",&name[0]))
    freetbl(res);
  else
    {
      freetbl(res);

      if(buildstmt(qrydat,QRY_INSERT,"articles",4))
	{
	  long id;
	  int i;
	  char attr[MAXRESSTR];

	  for(i = 0; i < 4; ++i)
	    {
	      buf = nextattr(buf,&attr[0]);
	      
	      if(i < 2)
		bindval(qrydat,&attr[0],SQL_C_CHAR);
	      else
		{
		  id = atol(attr);
		  bindval(qrydat,&id,SQL_C_SLONG);
		}
	    }
	  
	  if(execqry(qrydat,NULL))
	    return 1;
	}
      else
	return 1;
      
      if(newentrytbl(&name[0],buf,qrydat))
	return 1;
    }

  return 0;

}

int newentrytbl(char *name, char *cols, struct sqlqry *qrydat)
{

  char attr[MAXRESSTR];
  int colnum, i;
  
  cols = nextattr(cols,&attr[0]);
  colnum = atoi(attr);

  if(buildstmt(qrydat,QRY_CREATE,name,colnum))
    {
      for(i = 0; i < colnum; ++i)
	{
	  cols = nextattr(cols,&attr[0]);
	  
	  bindval(qrydat,&attr[0],SQL_VARNAME);
	}
      if(execqry(qrydat,NULL))
	return 1;
      else
	return 0;
    }
  else
    return 1;

}

DWORD WINAPI connmgr(LPVOID lstinfo)
{

  struct connmgrdat *info = (struct connmgrdat *) lstinfo;
  HANDLE sockmgr;
  struct sockmgrdat *sockmgrinfo;

  if(!initsockpool(info))
    {
      if(!initsockmgr(&sockmgrinfo,info->pool))
	sockmgr = CreateThread(NULL,0,sockmgrthrd,sockmgrinfo,0,&sockmgrinfo->thrdid);
      else
	return 1;

      while(1)
	{
	  SOCKET new;

	  if((new = WSAAccept(info->listensock,NULL,NULL,NULL,0)) == INVALID_SOCKET)
	    printf("Error accepting connection (code %d).\nRetrying...\n",WSAGetLastError());
	  else
	    addtosockpool(info->pool,new);
	}
    }
  else
    return 1;

  CloseHandle(info->pool->sockmtx);

  free(info->pool);

  return 0;

}

int initsockpool(struct connmgrdat *conn)
{
  
  struct sockpool *new = calloc(sizeof(struct sockpool),1);
  
  new->sockcount = 0;

  new->sockmtx = CreateMutex(NULL,FALSE,"sockmutex");

  conn->pool = new;

  return 0;

}

int addtosockpool(struct sockpool *pool, SOCKET sock)
{

  if(WaitForSingleObject(pool->sockmtx,INFINITE) == WAIT_OBJECT_0)
    {
      pool->conns[pool->sockcount++] = sock;

      ReleaseMutex(pool->sockmtx);

      return 0;
    }
  else
    return 1;

}

int initsockmgr(struct sockmgrdat **dat, struct sockpool *pool)
{

  struct sockmgrdat *new = malloc(sizeof(struct sockmgrdat));
  int i;

  new->pool = pool;

  if(!initqueue(&new->queue))
    {
      *dat = new;
      return 0;
    }
  else
    return 1;

}

int initqueue(struct bufqueue **queue)
{

  struct bufqueue *new = malloc(sizeof(struct bufqueue));

  new->first = NULL;
  new->last = NULL;

  if(new->qmtx = CreateMutex(NULL,FALSE,"queuemutex"))
    {
      *queue = new;
      return 0;
    }
  else
    return 1;

}

int initdatamgr(struct datamgrdat **dat, struct bufqueue *queue)
{

  struct datamgrdat *new = malloc(sizeof(struct datamgrdat));
  int i;

  new->queue = queue;
  
  new->threads = malloc(sizeof(struct thrddata *));

  for(i = 0; i < THREADNUM; ++i)
    {
      new->threads[i] = malloc(sizeof(struct thrddata));
      
      new->threads[i]->active = 0;

      //new->threads[i]->hdl = CreateThread(NULL,0,workerthrd,);////////////////////
    }

  *dat = new;

  return 0;

}

DWORD WINAPI workerthrd(LPVOID info)
{

  return 0;

}

DWORD WINAPI datamgrthrd(LPVOID info)
{

  return 0;


}

DWORD WINAPI sockmgrthrd(LPVOID info)
{

  struct sockmgrdat *data = (struct sockmgrdat *) info;

  while(1)
    {
      if(data->pool->sockcount)
	{
	  SOCKET new = nextsockconn(data->pool);
	  char buf[MAXLEN];

	  if(recv(new,buf,MAXLEN,0))
	    printf("%s\n",buf); ///////////

	  struct deviceconn *cinfo = malloc(sizeof(struct deviceconn));
	  cinfo->sock = new;
	  cinfo->wsabuf.buf = cinfo->buf;
	  cinfo->wsabuf.len = 50;
	  ZeroMemory((PVOID)&cinfo->ovl,sizeof(WSAOVERLAPPED));
	  DWORD flags = 0;
	  
	  WSARecv(new,&cinfo->wsabuf,1,NULL,&flags,&cinfo->ovl,readsockstream);
	}



    }

  return 0;


}

SOCKET nextsockconn(struct sockpool *pool)
{

  SOCKET retsock;

  if(WaitForSingleObject(pool->sockmtx,INFINITE) == WAIT_OBJECT_0)
    {
      retsock = pool->conns[--pool->sockcount];

      ReleaseMutex(pool->sockmtx);

      return retsock;
    }
  else
    return INVALID_SOCKET;

}

void CALLBACK readsockstream(DWORD err, DWORD len, LPWSAOVERLAPPED ovl, DWORD flags)
{printf("ddd %d\n",len);

  struct deviceconn *cinfo = (struct deviceconn *) ovl;

  printf("%s\n",cinfo->buf);

  WSARecv(cinfo->sock,&cinfo->wsabuf,1,NULL,0,&cinfo->ovl,readsockstream);

}

/*


struct deviceinfo
{
  
  //sqlinfo
  char tblname[MAXRESSTR];

};

struct deviceconn
{

  WSAOVERLAPPED ovl;

  SOCKET sock;

  struct deviceinfo *info;

  char buf[MAXLEN];

  WSABUF wsabuf;

};


struct thrddata
{

  char buf[MAXLEN];

  int active;

  HANDLE hdl;

  DWORD thrdid;

};

struct datamgrdat
{

  struct thrddata **threads;

  struct sockpool *pool;

  DWORD thrdid;

};

struct sockpool
{

  int sockcount;

  SOCKET conns[MAXCONNS];

  HANDLE sockmtx;

}

struct connmgrdat
{

  SOCKET listensock;

  struct sockpool *pool;

  DWORD thrdid;

};
*/
