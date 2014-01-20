<?php

session_start();
session_destroy();
$error = $_GET['error'];
?>

<html>
<head><?php include($_SERVER['DOCUMENT_ROOT'].'/head.php');?>  
<link rel="stylesheet" type="text/css" href="login.css">
</head>
<body>
<header>
<?php include($_SERVER['DOCUMENT_ROOT'].'/header.php');?>
</header>
<div id="page">
<section>

<?php

if($error){
	
	echo '<h2>!!!'.$error.'!!!</h2>';
}

?>
<form name="login" action="login.php" method="post">
<label>Username:</label><input type="text" name="username"><br/>
<label>Password:</label><input type="password" name="password"><br>
<button type="submit">Submit</button>
</form>
</section>
</div>
<footer>
<?php include($_SERVER['DOCUMENT_ROOT']."/footer.php");?>
</footer>
</body>
</html>