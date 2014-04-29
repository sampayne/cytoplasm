#define _WIN32_WINNT 0x0501
 
#include <winsock2.h>
#include <windows.h>
#include <ws2tcpip.h>
#include <sql.h> 
#include <sqlext.h> 
#include <stdio.h> 
#include <stdlib.h>
#include <time.h>

DWORD WINAPI parsestrm(LPVOID thrdheap);

#include "sqllib.h"
#include "sockserv.h"

int initsqlenv(struct sqlconn *sessinfo);
char *nextattr(char *str, char *buf, char dlm);
int parseart(char *buf, struct sqlqry *qrydat, struct sqlrestbl *res, struct entdat *ent);
int addentry(struct sqlqry *qrydat, struct sqlrestbl *res, char *buf, struct entdat *ent);
int searchtbl(struct sqlqry*, struct sqlrestbl *res, void *dat, char *col, char *tbl, char *cond);
int getperm(struct sqlqry *qrydat, struct sqlrestbl *res, struct entdat *ent);
int checkgroup(struct sqlqry *qrydat, struct sqlrestbl *res, struct entdat *ent);
int findval(struct sqlqry *, struct sqlrestbl *res, void *ret, char *col, char *tbl, char *cond);
int chktaxon(char *buf, struct sqlqry *qrydat, struct sqlrestbl *res);
int addtxn(struct sqlqry *qrydat, char *txn, SQLINTEGER sup);

int main(int argc, char **argv)
{

  SOCKET listensock, clientsock;
  const char *const port = (argc > 1) ? argv[1] : DEFAULT_PORT;
  HANDLE threads[MAXCONNS], curr;
  struct sqlconn sessinfo;


  if(argc < 2)
    printf("Note: usage is \"%s [port]\"\nUsing default port %s.\n",argv[0],port);


  if(initsqlenv(&sessinfo))
    return 1;


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
	commthread(clientsock,curr,&sessinfo);
      
    }

  closesocket(listensock);
  WSACleanup();

  return 0;

}

int initsqlenv(struct sqlconn *sessinfo)
{

  char *params[4] = {"localhost","test","root","hisnameisfrank"};

  printf("Connecting to SQL database.\n");
  
  if(makeconnstr(sessinfo->connstr,params,1))  
    if(!dbconn(sessinfo))
      {
	printf("Connected to SQL database.\n");
	return 0;
      }
  
  printf("Failed to connect to SQL database.\n");
  return 1;

}

DWORD WINAPI parsestrm(LPVOID thrdheap)
{

  int err = SOCKET_ERROR, i;
  struct sockmem *spc = (struct sockmem *) thrdheap;


  prepqry(spc->sessinfo->conn,&spc->qrydat);


  printf("[Socket %d] Receiving data...\n",spc->clientsock);


  if(recv(spc->clientsock,spc->buf,MAXLEN,0) > 0)
    { 
      char ent[MAXRESSTR], *nxt;

      printf("[Socket %d] Received: %s\n",spc->clientsock,spc->buf);
      
      sprintf(spc->buf,"%s-?",spc->buf);


      nxt = spc->buf;

      for(i = 0; i < 4; ++i)
	{
	  nxt = nextattr(nxt,&ent[0],'&');

	  *(((long *) &spc->ent) + i) = atol(ent);
	}


      if(!parseart(spc->buf,&spc->qrydat,&spc->res,&spc->ent))
	if(spc->ent.txn = chktaxon(spc->buf,&spc->qrydat,&spc->res))
	  {
	    memset(spc->buf,0,strlen(spc->buf));

	    while((err = recv(spc->clientsock,spc->buf,MAXLEN,0)) > 0)
	      {
		sprintf(spc->buf,"%s&?",spc->buf);

		printf("[Socket %d] Received: %s\n",spc->clientsock,spc->buf);
		
		addentry(&spc->qrydat,&spc->res,spc->buf,&spc->ent);

		memset(spc->buf,0,strlen(spc->buf));
	      }
	  }
    }


  if(err == SOCKET_ERROR)
    printf("[Socket %d] Error receiving data (code %d)\n",spc->clientsock,WSAGetLastError());
  else
    printf("[Socket %d] Client has closed connection.\n",spc->clientsock);


  closesocket(spc->clientsock);

  free(thrdheap);


  return 0;

}

int addentry(struct sqlqry *qrydat, struct sqlrestbl *res, char *buf, struct entdat *ent)
{

  char en[MAXRESSTR], *nxt = buf;
  SQLINTEGER id;
  int i = 0, j;

  printf("===Inserting entry into table...\n");


  nextattr(buf,&en[0],'?');
  en[strlen(en) - 1] = '\0';


  if(buildstmt(qrydat,QRY_INSERT,"cyentry",8))
    {
      time_t nw = time(NULL);

      for(j = 1; j < 6; ++j)
	bindval(qrydat,((long *)ent) + j,SQL_C_SLONG);

      bindval(qrydat,&en[0],SQL_C_CHAR);
      
      bindval(qrydat,&nw,SQL_C_SLONG);
      bindval(qrydat,&nw,SQL_C_SLONG);
      
      if(execqry(qrydat,NULL))
	return !!printf("===Failed to insert entry.\n");
    }
  else
    return 1;


  findval(qrydat,res,&id,"id","cyentry","1=1 order by id desc limit 1");
  

  if(buildstmt(qrydat,QRY_INSERT,"articleentry",2))
    {
      bindval(qrydat,&id,SQL_C_SLONG);
      bindval(qrydat,&ent->aid,SQL_C_SLONG);
      
      if(execqry(qrydat,NULL))
	return !!printf("===Failed to insert entry.\n");
    }
  else
    return 1;


  nxt = nextattr(nxt,&en[0],'&');
      
  while(en[0] != '?')
    {
      double val = atof(en);

      if(buildstmt(qrydat,QRY_INSERT,"entryvalue",3))
	{
	  bindval(qrydat,&id,SQL_C_SLONG);
	  bindval(qrydat,&val,SQL_C_DOUBLE);
	  bindval(qrydat,&i,SQL_C_SLONG);
	  
	  if(execqry(qrydat,NULL))
	    return !!printf("===Failed to insert entry.\n");
	}
      else
	return 1;
 
      nxt = nextattr(nxt,&en[0],'&');

      ++i;
    }

  printf("===Entry inserted successfully.\n");

  return 0;

}

char *nextattr(char *str, char *buf, char dlm)
{

  int i;

  for(i = 0; i < strlen(str); ++i)
    if(str[i] == dlm)
      break;
    else
      buf[i] = str[i];

  buf[i++] = '\0';

  return str + i;

}

int parseart(char *buf, struct sqlqry *qrydat, struct sqlrestbl *res, struct entdat *ent)
{

  printf("===Checking if Article and User IDs are present...\n");


  if(!searchtbl(qrydat,res,&ent->aid,"id","article",NULL))
    {

      if(!searchtbl(qrydat,res,&ent->uid,"id","user",NULL))
	{
	  printf("===Article and User IDs are present.\n");
	  printf("===Checking if user has appropriate permissions...\n");

	  if(!getperm(qrydat,res,ent))
	    return !printf("===User has permission to access data.\n");
	  else
	    return !!printf("===User does not have sufficient privileges.\n");
	}
    }

  return !!printf("===Article and/or User ID not found.\n");

}

int getperm(struct sqlqry *qrydat, struct sqlrestbl *res, struct entdat *ent)
{

  char cond[16];

  sprintf(cond,"id = %Ld",ent->aid);

  findval(qrydat,res,&ent->hd,"hidden","article",&cond[0]);

  if(!ent->hd)
    {
      if(!searchtbl(qrydat,res,&ent->hd,"secure","article",&cond[0]))
	return 0;
    }
  else
    {
      sprintf(cond,"userID = %Ld",ent->uid);

      if(!searchtbl(qrydat,res,&ent->aid,"articleID","usercreator",&cond[0]))
	return 0;
      else
	return checkgroup(qrydat,res,ent);
    }

  return 1;

}

int searchtbl(struct sqlqry *qrydat, struct sqlrestbl *res, void *dat, char *col, char *tbl, char *cond)
{

  int fnd;

  if(buildstmt(qrydat,QRY_SELECT,tbl,1))
    {
      bindval(qrydat,col,SQL_VARNAME);

      if(cond)	
	addcond(qrydat,cond);

      if(execqry(qrydat,res))
	return 1;
    }
  else
    return 1;


  fnd = searchcol(res,col,dat);

  freetbl(res);
  return fnd;

}

int checkgroup(struct sqlqry *qrydat, struct sqlrestbl *res, struct entdat *ent)
{

  SQLINTEGER gid, chk;
  char cond[16];

  sprintf(cond,"userID = %Ld",ent->uid);
  
  if(!findval(qrydat,res,&gid,"groupID","groupuser",&cond[0]))
    {
      sprintf(cond,"articleID = %Ld",ent->aid);

      findval(qrydat,res,&chk,"groupID","groupcreator",&cond[0]);
 
      while(gid != chk)
	{
	  if(gid == -1)
	    return 1;
	  
	  sprintf(cond,"id = %Ld",gid);

	  findval(qrydat,res,&gid,"super","cygroup",&cond[0]);
	}

      return 0;
    }

}

int findval(struct sqlqry *qrydat, struct sqlrestbl *res, void *ret, char *col, char *tbl, char *cond)
{

  if(buildstmt(qrydat,QRY_SELECT,tbl,1))
    {
      bindval(qrydat,col,SQL_VARNAME);
      
      if(cond)
	addcond(qrydat,cond);
      
      if(!execqry(qrydat,res))
	{
	  getval(res,col,ret);

	  freetbl(res);
	  return 0;
	}
      else
	return 1;
    }
  else
    return 1;

}

int chktaxon(char *buf, struct sqlqry *qrydat, struct sqlrestbl *res)
{

  char txn[MAXRESSTR], *nxt = buf, cond[32];
  SQLINTEGER sup = -1;
  int i;

  printf("===Checking if required taxonomies are present...\n");

  for(i = 0; i < 4; ++i)
    nxt = nextattr(nxt,&txn[0],'&');

  nxt = nextattr(nxt,&txn[0],'-');
      
  while(txn[0] != '?')
    {
      sprintf(cond,"name = \'%s\'",&txn[0]);
 
      if(searchtbl(qrydat,res,&txn[0],"name","cytaxonomy",NULL))
	{
	  printf("===Taxonomy \"%s\" not found. Inserting...\n");
	  addtxn(qrydat,&txn[0],sup);
	  printf("===Taxonomy \"%s\" inserted.\n");
	}

      findval(qrydat,res,&sup,"id","cytaxonomy",&cond[0]);

      nxt = nextattr(nxt,&txn[0],'-');
    }

  printf("===All required taxonomies have been located.\n");

  return sup;

}

int addtxn(struct sqlqry *qrydat, char *txn, SQLINTEGER sup)
{

  if(buildstmt(qrydat,QRY_INSERT,"cytaxonomy",3))
    {
      SQLINTEGER nw = 0;

      bindval(qrydat,txn,SQL_C_CHAR);
      bindval(qrydat,&sup,SQL_C_SLONG);
      bindval(qrydat,&nw,SQL_C_SLONG);

      if(execqry(qrydat,NULL))
	return 1;
     else
       return 0;
    }
  else
    return 1;

}
