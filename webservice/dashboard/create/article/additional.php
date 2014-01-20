<?php session_start();

if(!isset($_SESSION['userID'])){
session_destroy();	
header("Location: /login");}

$additional = $_POST['additional'];
$error = $_GET['error'];

if($additional < 1){
	

	
	
}


?>

<html>
<head><?php include($_SERVER['DOCUMENT_ROOT'].'/head.php');?>  
 <link rel="stylesheet" type="text/css" href="createarticle.css">

</head>
<body>
<header>
<?php include($_SERVER['DOCUMENT_ROOT'].'/header.php');?>
</header>
<div id="page">
<section>

<?php

if($error != ""){
	
	echo '<h2>!!!'.$error.'!!!</h2>';
}
?>
<form name="create_article" action="create.php" method="post">
<input type="hidden" name="name" value="<?php echo $_POST['name'];?>">
<input type="hidden" name="description" value="<?php echo $_POST['description'];?>">
<input type="hidden" name="owner" value="<?php echo $_POST['owner'];?>">
<input type="hidden" name="hidden" value="<?php echo $_POST['hidden'];?>">
<input type="hidden" name="secure" value="<?php echo $_POST['secure'];?>">
<input type="hidden" name="additional" value="<?php echo $_POST['additional'];?>">
<?php

for($i = 0; $i < $additional; $i++){
	
	echo '<label>Name:</label><input type="text" name="adname.'.$i.'">';
	echo '<label>Value:</label><input type="text" name="adval'.$i.'">';
	echo'<br/>';
	
}

?>

<button type="submit" name="Submit">Continue</button>
</form>
</section>
</div>
<footer>
<?php include($_SERVER['DOCUMENT_ROOT']."footer.php");?>
</footer>
</body>
</html>