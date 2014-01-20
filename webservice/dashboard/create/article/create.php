

<?php
session_start();

if (!isset($_SESSION['userID'])) {
    session_destroy();
    header("Location: /login");
} else {
    
    
    
    include($_SERVER['DOCUMENT_ROOT'] . '/database.php');
    $database = new Database;
    
    $name              = $_POST['name'];
    $description       = $_POST['description'];
    $additional        = $_POST['additional'];
    $owner             = $_POST['owner'];
    $hidden            = $_POST['hidden'];
    $secure            = $_POST['secure'];
    
    $additional_fields = "";
    
    for($i = 0; $i < $additional; $i++){
	    
	 $field = $_POST['adname.'.$i];
	 $value = $_POST['adval'.$i];
	 $additional_fields = $additional_fields.$field.",".$value."/";
	    
	    
    }
    
    if ($hidden) {
        
        $hidden = 1;
        $secure = 1;
        
    }
    
    $sqlString = "INSERT INTO Article (name, description, secure, hidden,additional_fields) VALUES(?,?,?,?,?)";
    $database->sqlInsert($sqlString, array(
        $name,
        $description,$secure,$hidden,$additional_fields
    ));
    
    $articleID = $database->getNewestID("Article");
    
    if(substr_compare($owner, "u", 0, 1) == 0){
	    
	    $userID = substr($owner, 1);
	    
	    $sqlString = "INSERT INTO UserOwner (articleID,userID) VALUES (?,?)";
	    $database->sqlInsert($sqlString,array($articleID,$userID));
	    
    }else if(substr_compare($owner, "g", 0, 1) == 0){
	    
	    $groupID = substr($owner, 1);
	    $sqlString = "INSERT INTO GroupCreator (articleID,groupID) VALUES (?,?)";
	    $database->sqlInsert($sqlString,array($articleID,$groupID));
	    
    }else{
	    
	    echo 'ERROR - OWNER ID NOT RECOGNISED';
	    
    }
 ?>
 
 <html><head>
	<?php include($_SERVER['DOCUMENT_ROOT'].'/head.php'); ?>
	<link rel="stylesheet" href="createarticle.css" title="Create Group" type="text/css" media="screen" charset="utf-8">
	
 </head>
<body>

<header>
<?php include($_SERVER['DOCUMENT_ROOT'].'/header.php'); ?>

</header>


<section>
    <h2>Article Added!</h2><br>
 	<p class="link"><a href="/dashboard">Click Here to Return to your Dashboard</a></p>
	</section>
	<footer><?php include($_SERVER['DOCUMENT_ROOT'].'/footer.php'); ?></footer>
	</body></html>
 	
 	<?php
}


?>