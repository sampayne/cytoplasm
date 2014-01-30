#include <windows.h> 
#include <sql.h> 
#include <sqlext.h> 
#include <stdio.h> 
#include <stdlib.h>

enum {MAXQRYLEN = 1024,
      MAXCONNSTR = 1024,
      MAXERRBUF = 512,
      MAXCOLNAME = 96,
      MAXRESSTR = 256,
      SQL_VARNAME = 32};

typedef enum {QRY_SELECT,
	      QRY_INSERT,
	      QRY_CREATE} qrytype;

struct sqlconn
{

  SQLHENV env;

  SQLHDBC conn;

  char connstr[MAXCONNSTR];

};

struct sqlqry
{

  SQLHSTMT qry;

  qrytype type;

  char qrystr[MAXQRYLEN];

};

struct sqlerrvals
{

  SQLCHAR code[6], msg[MAXERRBUF];
  
  SQLINTEGER errbuf;
  
  SQLSMALLINT retlen;

};

struct sqlrestbl
{

  SQLSMALLINT colnum, *types;

  SQLCHAR **names;

  struct sqlqry *qrydat;

  struct sqlrow *first, *curr;
  
};

typedef struct sqlrow
{

  struct sqlrestbl *tbl;
  
  struct sqlcol **cols;
  
  struct sqlrow *next;
  
} SQLROW;

struct sqlcol
{

  struct sqlrestbl *tbl;

  SQLSMALLINT id;

  union
  {
    
    SQLCHAR str[MAXRESSTR];
    
    SQLINTEGER num;
    
    SQLDOUBLE dbl;
    
  } val;

};

int dbconn(struct sqlconn *sessinfo);
int makeconnstr(char *connstr, char **params, int login);
int drvconn(SQLHDBC conn, char *connstr);
int sqlerr(SQLHANDLE hdl, SQLSMALLINT htype);
int prepqry(SQLHDBC conn, struct sqlqry *qrydat);
int buildstmt(struct sqlqry *qrydat, qrytype type, char *tbl, int params);
int bindval(struct sqlqry *qrydat, void *val, const SQLSMALLINT type);
int splitstr(char *p1, char *p2, char *str);
int execqry(struct sqlqry *qrydat, struct sqlrestbl *res);
int inittbl(struct sqlqry *qrydat, struct sqlrestbl *res);
int initrow(struct sqlrestbl *res);
int initcols(struct sqlrestbl *res, struct sqlrow *row);
int getcoltypes(struct sqlrestbl *res);
int storenextrow(struct sqlrestbl *res);
int freetbl(struct sqlrestbl *res);
SQLROW *getnextrow(struct sqlrestbl *results);
void *getcolbyindex(SQLROW *row, int index);
void *getcolbyname(SQLROW *row, char *name);
int searchcol(struct sqlrestbl *res, char *colname, void *val);

int main(int argc, char **argv)
{

  struct sqlconn sessinfo;
  struct sqlqry qrydat;
  struct sqlrestbl res;

  char *params[2] = {"(local)","master"};

  makeconnstr(sessinfo.connstr,params,0);

  dbconn(&sessinfo);

  prepqry(sessinfo.conn,&qrydat);

  buildstmt(&qrydat,QRY_SELECT,"articles",0);

  /* buildstmt(&qrydat,QRY_CREATE,"Table2",3);

  printf("%s\n",qrydat.qrystr);
  char *col1 = "tst1";
  bindval(&qrydat,&col1,SQL_VARNAME);
  char *col2 = "tst2";
  bindval(&qrydat,&col2,SQL_VARNAME);
  char *col3 = "tst3";
  bindval(&qrydat,&col3,SQL_VARNAME);*/


  if(execqry(&qrydat,&res))
    return 1;


  char *name = "tstart7";
  if(searchcol(&res,"name",&name))
    {
      freetbl(&res);
      
      buildstmt(&qrydat,QRY_CREATE,name,3);
      
      char *col1 = "tst1";
      bindval(&qrydat,&col1,SQL_VARNAME);
      char *col2 = "tst2";
      bindval(&qrydat,&col2,SQL_VARNAME);
      char *col3 = "tst3";
      bindval(&qrydat,&col3,SQL_VARNAME);
      
      execqry(&qrydat,&res);
      freetbl(&res);
      buildstmt(&qrydat,QRY_INSERT,"articles",4);

      char *taxon = "ecg";
      long sensor = 100, transmitter = 89;
      
      bindval(&qrydat,&name,SQL_C_CHAR);
      bindval(&qrydat,&taxon,SQL_C_CHAR);
      bindval(&qrydat,&sensor,SQL_C_SLONG);
      bindval(&qrydat,&transmitter,SQL_C_SLONG);

      execqry(&qrydat,&res);
      freetbl(&res);

  buildstmt(&qrydat,QRY_SELECT,"articles",0);

      execqry(&qrydat,&res);

      freetbl(&res);
    }

  return 0;

  SQLROW *curr = getnextrow(&res);

  char *str = getcolbyindex(curr,3);

  double *dbl = getcolbyname(curr,"tst3");

  printf("%s\n%Lf\n",str,*dbl);

  freetbl(&res);

  buildstmt(&qrydat,QRY_INSERT,"Table1",3);

  long x = 567;
  double y = 444.89;
  char *z = "anothertest";

  bindval(&qrydat,&x,SQL_C_SLONG);
  bindval(&qrydat,&y,SQL_C_DOUBLE);
  bindval(&qrydat,&z,SQL_C_CHAR);

  execqry(&qrydat,&res);

  return 0; 

}

int dbconn(struct sqlconn *conninfo)
{
  
  printf("Initialising SQL environment... ");

  if(SQLAllocHandle(SQL_HANDLE_ENV,SQL_NULL_HANDLE,&conninfo->env) ^ ~0)
    if(SQLSetEnvAttr(conninfo->env,SQL_ATTR_ODBC_VERSION,(SQLPOINTER)SQL_OV_ODBC3,0) ^ ~0)
      if(SQLAllocHandle(SQL_HANDLE_DBC,conninfo->env,&conninfo->conn) ^ ~0)
	{
	  printf("done.\nConnecting to database... ");
	  return drvconn(conninfo->conn,conninfo->connstr);
	}
      else
	return sqlerr(conninfo->conn,SQL_HANDLE_DBC);

  return sqlerr(conninfo->env,SQL_HANDLE_ENV);

}

int makeconnstr(char *connstr, char **params, int login)
{

  int len;


  printf("Creating connection string... ");

  len = sprintf(connstr,"DRIVER=SQL Server;SERVER=%s;",params[0]);

  if(login)
    len += sprintf(connstr+len,"UID=%s;PWD=%s;DATABASE=%s;",params[2],params[3],params[1]);
  else
    len += sprintf(connstr+len,"Trusted_Connection=Yes;DATABASE=%s;",params[1]);

  printf("done.\nConnection string: \"%s\"\n",connstr);


  return len;

}

int sqlerr(SQLHANDLE hdl, SQLSMALLINT htype)
{

  struct sqlerrvals errvals;

  SQLGetDiagRec(htype,hdl,1,errvals.code,&errvals.errbuf,errvals.msg,MAXERRBUF,&errvals.retlen);


  printf("Failed.\nSQLSTATE: %s\nSystem message: %s\n",errvals.code,errvals.msg);

  return 1;

}

int drvconn(SQLHDBC conn, char *connstr)
{

  SQLCHAR res[MAXCONNSTR];
  SQLSMALLINT reslen;


  if(SQLDriverConnect(conn,NULL,connstr,SQL_NTS,res,sizeof(res),&reslen,SQL_DRIVER_NOPROMPT) & ~1)
    return sqlerr(conn,SQL_HANDLE_DBC);


  printf("done.\n");

  return 0;

}

int prepqry(SQLHDBC conn, struct sqlqry *qrydat)
{

  printf("Preparing query handle... ");

  if(SQLAllocHandle(SQL_HANDLE_STMT,conn,&qrydat->qry) & ~1)
    return sqlerr(qrydat->qry,SQL_HANDLE_STMT);
  else
    {
      printf("done.\n");
      return 0;
    }

}

int buildstmt(struct sqlqry *qrydat, qrytype type, char *tbl, int params)
{

  int len, i;

  printf("Building query template... ");


  if(!type)
    len = sprintf(qrydat->qrystr,"SELECT * FROM %s;",tbl);
  else if(type == QRY_INSERT)
    {
      len = sprintf(qrydat->qrystr,"INSERT INTO %s VALUES (",tbl);

      for(i = 0; i < params; ++i)
	sprintf(qrydat->qrystr + len + (i << 1),"%c%c%c",'?',(i == (params - 1)) ? ')' : ',',';');
    }
  else
    {
      len = sprintf(qrydat->qrystr,
		    "CREATE TABLE %s (%s_id int IDENTITY(1,1) PRIMARY KEY,",tbl,tbl);

     for(i = 0; i < params; ++i)
	len += sprintf(qrydat->qrystr + len,"%s","? float,");

     *(qrydat->qrystr + len - 1) = ')';
    }

  qrydat->type = type;


  printf("done.\n");

  return len;

}

int bindval(struct sqlqry *qrydat, void *val, const SQLSMALLINT type)
{

  char p1[MAXQRYLEN>>1] = "", p2[MAXQRYLEN>>1] = "";
  int len;

  splitstr(p1,p2,qrydat->qrystr);

  switch(type)
    {
    case SQL_C_SLONG : len = sprintf(qrydat->qrystr,"%s%Ld%s",p1,*((long *)val),p2);
      break;

    case SQL_C_DOUBLE : len = sprintf(qrydat->qrystr,"%s%Lf%s",p1,*((double *)val),p2);
      break;

    case SQL_C_CHAR : len = sprintf(qrydat->qrystr,"%s\'%s\'%s",p1,(char *)val,p2);
      break;

    case SQL_VARNAME : len = sprintf(qrydat->qrystr,"%s%s%s",p1,(char *)val,p2);
      break;
    }

  return len;

}

int splitstr(char *p1, char *p2, char *str)
{

  int i,j;

  for(i = 0; i < MAXQRYLEN<<1; ++i)
    if(str[i] == '?')
      break;
    else
      p1[i] = str[i];

  for(j = 0; str[i]; ++j)
    p2[j] = str[++i];

  return 0;

}

int execqry(struct sqlqry *qrydat, struct sqlrestbl *res)
{

  int i;

  printf("Executing query \"%s\"... ",qrydat->qrystr);

  if(SQLExecDirect(qrydat->qry,qrydat->qrystr,strlen(qrydat->qrystr)) ^ ~0)
    {
      printf("done.\n");

      if(qrydat->type)
	return 0;
    }
  else
    return sqlerr(qrydat->qry,SQL_HANDLE_STMT);


  printf("Building results table... ");

  if(!inittbl(qrydat,res))
    if(!getcoltypes(res))
      {
	printf("done.\n");
	return 0;
      }
  
  printf("Failed, out of memory.\n");
  return -1;

}

int inittbl(struct sqlqry *qrydat, struct sqlrestbl *res)
{

  int i;

  if(SQLNumResultCols(qrydat->qry,&res->colnum) & ~1)
    return sqlerr(qrydat->qry,SQL_HANDLE_STMT);

  res->qrydat = qrydat;

  if(!(res->types = malloc(sizeof(SQLSMALLINT) * res->colnum)))
    return -1;

  res->first = NULL;
  res->curr = NULL;
  
  if(res->names = malloc(sizeof(SQLCHAR *) * res->colnum))
     {
      for(i = 0; i < res->colnum; ++i)
	if(!(res->names[i] = malloc(sizeof(SQLCHAR) * MAXCOLNAME)))
	  return -1;

      return 0;
     }

  return -1;

}

int getcoltypes(struct sqlrestbl *res)
{

  SQLUSMALLINT i;
  SQLSMALLINT namelen, decdig, nulls;
  SQLULEN colsize;

  for(i = 0; i < res->colnum; ++i)
    {
      if(SQLDescribeCol(res->qrydat->qry,i+1,res->names[i],(SQLSMALLINT)MAXCOLNAME,
			&namelen,&res->types[i],&colsize,&decdig,&nulls) & ~1)
	return sqlerr(res->qrydat->qry,SQL_HANDLE_STMT);

      switch(res->types[i])
	{
	case SQL_INTEGER : res->types[i] = SQL_C_SLONG;
	  break;

	case SQL_FLOAT : res->types[i] = SQL_C_DOUBLE;
	  break;

	case SQL_WLONGVARCHAR : res->types[i] = SQL_C_CHAR;
	  break;
	}
    }
  return 0;

}

int initrow(struct sqlrestbl *res)
{

  struct sqlrow *new;

  if(!(new = malloc(sizeof(struct sqlrow))))
    return -1;

  new->tbl = res;
  new->next = NULL;

  if(!res->first)
    res->first = new;
  else
    res->curr->next = new;

  res->curr = new;
  
  initcols(res,new);
  
  return 0;

}

int initcols(struct sqlrestbl *res, struct sqlrow *row)
{
  
  SQLSMALLINT i;

  if(row->cols = malloc(sizeof(struct sqlcol *) * res->colnum))
    {
      for(i = 0; i < res->colnum; ++i)
	{
	  if(row->cols[i] = malloc(sizeof(struct sqlcol)))
	    {
	      row->cols[i]->tbl = res;
	      row->cols[i]->id = i;
	    }
	  else
	    return -1;
	}
      return 0;
    }

  return -1;

}

int storenextrow(struct sqlrestbl *res)
{
  
  SQLSMALLINT i;

  if(initrow(res))
    return -1;

  for(i = 0; i < res->colnum; ++i)
    {
      SQLPOINTER val;
      SQLLEN size, retlen;

      switch(res->types[i])
	{
	case SQL_C_SLONG :
	  val = &res->curr->cols[i]->val.num;
	  size = sizeof(SQLINTEGER);
	  break;

	case SQL_C_DOUBLE : 
	  val = &res->curr->cols[i]->val.dbl;
	  size = sizeof(SQLDOUBLE);
	  break;

	case SQL_C_CHAR :
	  val = &res->curr->cols[i]->val.str;
	  size = sizeof(res->curr->cols[i]->val.str);
	  break;
	}

      if(SQLBindCol(res->qrydat->qry,i+1,res->types[i],val,size,&retlen) & ~1)
	return sqlerr(res->qrydat->qry,SQL_HANDLE_STMT);
    }
  
  if(SQLFetch(res->qrydat->qry) == SQL_NO_DATA)
    return SQL_NO_DATA;
  else
    return 0;

}

int freetbl(struct sqlrestbl *res)
{
  
  int i;
  struct sqlrow *curr;

  printf("Closing current query... ");

  if(res->qrydat->type)
    {
      printf("done.\n");
      return 0;
    }
  
  free(res->types);

  for(i = 0; i < res->colnum; ++i)
    free(res->names[i]);
  
  free(res->names);

  curr = res->first;

  while(curr)
    {
      for(i = 0; i < res->colnum; ++i)
	free(curr->cols[i]);
      
      free(curr->cols);
      free(curr);

      curr = curr->next;
    }

  if(SQLCloseCursor(res->qrydat->qry) & ~1)
    return sqlerr(res->qrydat->qry,SQL_HANDLE_STMT);

  free(res);

  printf("done.\n");

  return 0;

}

SQLROW *getnextrow(struct sqlrestbl *results)
{

  if(storenextrow(results))
    return NULL;
  else
    return results->curr;

}

void *getcolbyindex(SQLROW *row, int index)
{

  switch(row->tbl->types[index])
    {
    case SQL_C_SLONG : return &row->cols[index]->val.num;

    case SQL_C_DOUBLE : return &row->cols[index]->val.dbl;

    case SQL_C_CHAR : return &row->cols[index]->val.str;
    }

  return NULL;

}

void *getcolbyname(SQLROW *row, char *name)
{
  
  int i;
  
  for(i = 0; i < row->tbl->colnum; ++i)
    if(!strcmp(name,row->tbl->names[i]))
      break;
  
  return getcolbyindex(row,i);

}

int searchcol(struct sqlrestbl *res, char *colname, void *val)
{

  int i;

  if(!res->first)
    getnextrow(res);
  
  for(i = 0; i < res->colnum; ++i)
    if(!strcmp(colname,res->names[i]))
      break;

  do
    {
      switch(res->types[i])
	{
	case SQL_C_SLONG :
	  if((*(long *)getcolbyindex(res->curr,i)) == *(long *)val)
	    return 0;
	  else
	    break;

	case SQL_C_DOUBLE : 
	  if((*(double *)getcolbyindex(res->curr,i)) == *(double *)val)
	    return 0;
	  else
	    break;

	case SQL_C_CHAR :
	  if(!strcmp((char *)getcolbyindex(res->curr,i),*(char **)val))
	    return 0;
	  else
	    break;
	}
    } while(getnextrow(res));
  
  return 1;

}
