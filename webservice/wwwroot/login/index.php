<?php
require($_SERVER['DOCUMENT_ROOT'].'/api/include.php');

if(isset($_POST['commit'])){

    session_start();
    session_destroy();
    session_start();

    $password = hash('sha512', $_POST['password']);

	$user =  LoginFactory::login($_POST['username'],$password);
        
	if(!isset($user->error)){
       
        session_start();
		$_SESSION['user'] = serialize($user);
		header('Location: /dashboard');	
		

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
<section>
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