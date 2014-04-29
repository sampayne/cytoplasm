enum {PORT_OPEN_ERROR,
      DCB_GET_ERROR,
      DCB_SET_ERROR,
      TIMEOUT_SET_ERROR};

int serialerr(int code, void *data);

int serialerr(int code, void *data)
{

  char *msg;

  switch(code)
    {
    case 0 : msg = "opening port";
      break;
    case 1 : msg = "obtaining DCB settings";
      break;
    case 2 : msg = "setting DCB configuration";
      break;
    case 3 : msg = "setting timeout settings";
      break;
    default : msg = "unknown";
    }

  printf("Error %s (code %d)\n",msg,GetLastError());
  
  return 1;

}
