<?php
session_start();

if (!isset($_SESSION['userID'])) {
    session_destroy();
    header("Location: /login");
} else {
    
    
    
    include($_SERVER['DOCUMENT_ROOT'] . '/database.php');
    $database = new Database;
    $name              = $_POST['name'];
    $parent       	   = $_POST['parent'];

	if($name != "" && $parent != ""){ ?>
	
<html><head>
	<?php include($_SERVER['DOCUMENT_ROOT'].'/head.php'); ?>
	<link rel="stylesheet" href="creategroup.css" title="Create Group" type="text/css" media="screen" charset="utf-8">
	
 </head>
<body>

<header>
<?php include($_SERVER['DOCUMENT_ROOT'].'/header.php'); ?>

</header>

	<?php
    $sqlString = "INSERT INTO cyGroup (name, super) VALUES (?,?)";
    $database->sqlInsert($sqlString, array(
        $name,$parent
    ));
    
    $groupID = $database->getNewestID("cyGroup");?>
    
    <section>
    <h2>Group Created!</h2><br>
 	
 	
 	<?php
    
    if($parent == -1){
	    
	    echo '<p>You are now an Admin for the Group '.$name.'</p><br>';
	    
	    $sqlString = "INSERT INTO GroupUser(userID,groupID) VALUES(?,?)";
	    $database->sqlInsert($sqlString, array(
		  		$_SESSION['userID'],$groupID));
		  		
		$sqlString = "INSERT INTO GroupAdmin(userID,groupID) VALUES(?,?)";
	    $database->sqlInsert($sqlString, array(
		  		$_SESSION['userID'],$groupID));
		  		$_SESSION['admin'] = 1;
	    
    }
     ?>
	<p class="link"><a href="/dashboard">Click Here to Return to your Dashboard</a></p>
	</section>
	<footer><?php include($_SERVER['DOCUMENT_ROOT'].'/footer.php'); ?></footer>
	</body></html>
	
	
	<?php
}else{
	
	header("Location: /dashboard");
	
	
}
       
}


?>