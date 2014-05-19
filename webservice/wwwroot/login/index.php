<?php
require($_SERVER['DOCUMENT_ROOT'].'/api/include.php');

if(isset($_POST['commit'])){

    session_start();
    session_unset();
    session_destroy();

    $password = hash('sha512', $_POST['password']);

	$CURRENT_USER =  LoginFactory::login($_POST['username'],$password);
        
	if(!isset($CURRENT_USER->error)){
       
        session_start();
		$_SESSION['user'] = serialize($CURRENT_USER);
		header('Location: /dashboard/');	
        die();
	}

}

?>

<html>
<head><?php require($_SERVER['DOCUMENT_ROOT'].'/defaults/head.php');?>  
<link rel="stylesheet" type="text/css" href="login.css">
</head>
<body>

<?php require($_SERVER['DOCUMENT_ROOT'].'/defaults/header/header.php');?>

<div class="page">
            <h1>Login</h1>

<section>

<?php if(isset($CURRENT_USER->error)):?>
<p class="error"> <?php echo $CURRENT_USER->error?> </p>
<?php endif ?>
<form name="login" action="/login/" method="post">
    <input type="hidden" name="commit" value="1">
   <input type="text" name="username" placeholder="Username...">
    <input type="password" name="password" placeholder="Password...">
<button type="submit">Submit</button>
</form>
</section>
</div>
<?php require($_SERVER['DOCUMENT_ROOT'].'/defaults/footer/footer.php');?>
</body>
</html>