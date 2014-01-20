<?php session_start();

if(!isset($_SESSION['userID'])){
session_destroy();	
header("Location: /login");}

$error = $_GET['error'];


include_once($_SERVER['DOCUMENT_ROOT']."/group_manager.php");

$userID = $_SESSION['userID'];
$groupManager = new GroupManager;
$groups = $groupManager->getGroups($userID,1);

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

if($error){
	
	echo '<h2>!!!'.$error.'!!!</h2>';
}

?>
<form name="create_article" action="additional.php" method="post">
<label>Name:</label><input type="text" name="name"><br/>
<label>Description:</label><input type="text" name="description"><br>
<label>No. of Additional Fields:</label><input type="number" name="additional"><br>
<label>Hidden:</label><input type="checkbox" name="hidden"><br/>
<label>Secure:</label><input type="checkbox" name="secure"><br/>
<label>Owner:</label>
<select name="owner">
  <option value="u<?php echo $_SESSION['userID'];?>" selected="selected" >Me</option>

    <?php
    
					foreach($groups as $group){
						
						echo '<option value="g'.$group[0].'">'.$group[1].'</option>';  

					}                                                         
			?>


</select>
<br>
<input type="submit" name="Submit">
</form>
</section>
</div>
<footer>
<?php include($_SERVER['DOCUMENT_ROOT']."footer.php");?>
</footer>
</body>
</html>