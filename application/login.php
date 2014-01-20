<?php

$method = $_SERVER['REQUEST_METHOD'];
if( strtolower($method) == 'post')
{

	if (empty($_POST['username']) && empty($_POST['password']))
	{
 	 echo "PLEASE ENTER YOUR USERNAME AND PASSWORD";
  		exit();
	}
	else
	{
			echo "true";
	}
	
}
?>