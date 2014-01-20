<?php

session_start();
$error = $_GET['error'];


include_once($_SERVER['DOCUMENT_ROOT']."/group_manager.php");

$userID = $_SESSION['userID'];
$groupManager = new GroupManager;
$groups = $groupManager->getGroups($userID,1);

?>

<html>
<head><?php include($_SERVER['DOCUMENT_ROOT'].'/head.php');?>  
<link rel="stylesheet" rel="stylesheet" type="text/css" href="createuser.css">
</head>
<body>
<header>
<?php include($_SERVER['DOCUMENT_ROOT'].'/header.php');?>
</header>
<div id="page">
<section>
<form name="signup" action="create.php" method="post">
<?php

if($error){
	
	echo '<h2>!!!'.$error.'!!!</h2>';
}

?><label>Name:</label><input type="text" name="name"><br/>
<label>Username:</label><input type="text" name="username"><br/>
<label>Password:</label><input type="password" 
name="password"><br>
<label>Group:</label>

<select name="group">
 

    <?php
    
					foreach($groups as $group){
						
						echo '<option value="'.$group[0].'">'.$group[1].'</option>';  

					}                                                         
			?>


</select>
	
	
</select>
<input type="submit" name="Submit">
</form></section>
</div>
<footer>
<?php include($_SERVER['DOCUMENT_ROOT']."/footer.php");?>
</footer>
</body>
</html>