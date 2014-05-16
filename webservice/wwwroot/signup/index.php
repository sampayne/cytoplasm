<?php

session_start();
session_destroy();
$error = $_GET['error'];
?>

<html>
<head><?php include($_SERVER['DOCUMENT_ROOT'].'/head.php');?>  
<link rel="stylesheet" rel="stylesheet" type="text/css" href="signup.css">
</head>
<body>
<header>
<?php include($_SERVER['DOCUMENT_ROOT'].'/header.php');?>
</header>
<div class="page">
<section>
<form name="signup" method="post">
<?php

if($error){
	
	echo '<h2>!!!'.$error.'!!!</h2>';
}

?><label>Name:</label><input type="text" name="name"><br/>
<label>Username:</label><input type="text" name="username"><br/>
<label>Password:</label><input type="password" name="password"><br>
<input type="submit" name="Submit">
</form></section>
</div>
<footer>
<?php include($_SERVER['DOCUMENT_ROOT']."/footer.php");?>
</footer>
</body>
</html>