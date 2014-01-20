<?php

session_start();

if(!isset($_SESSION['userID'])){
session_destroy();	
header("Location: /login");
}

include_once($_SERVER['DOCUMENT_ROOT']."/group_manager.php");
include_once($_SERVER['DOCUMENT_ROOT']."/article_manager.php");
include_once($_SERVER['DOCUMENT_ROOT']."/data_manager.php");
$userID = $_SESSION['userID'];

$groupManager = new GroupManager;
$articleManager = new ArticleManager;
$data_manager = new DataManager;
$dataScore = $data_manager->getDataScore($userID);

$groups = $groupManager->getGroups($userID,1);
$articles = $articleManager->getArticlesByUser($userID);

?>

<!DOCTYPE html>
<html>
<head>
<?php include($_SERVER['DOCUMENT_ROOT'].'/head.php');?>  
<link rel="stylesheet" type="text/css" href="dashboard.css">

</head>
<body>
<header>
<?php include($_SERVER['DOCUMENT_ROOT'].'/header.php');?>
</header>
<div id="page">
<h1>Welcome <?php echo $_SESSION['name'];?></h1>
<section>
<h2>Actions</h2>
<h3><a href="create/group">Create a Group/Subgroup</a></h3>
<h3><a href="create/article">Create an Article</a></h3>
<h3><a href="contribute">Contribute Data</a></h3>
<?php 
	
	if($_SESSION['admin'] > 0){
		
		echo '<h3><a href="create/user">Create a new User</a></h3>';
		
	}
	
?>



</section>
<section>
<h2>Your Groups</h2>


<?php	

foreach($groups as $group){
						
						
						
						echo '<h3><a href=/group/?id='.$group[0].'>'.$group[1].'</a></h3>';  

		}   
		
		
?>                


</section>

<section>
<h2>Your Articles</h2>
<?php foreach($articles as $article){
						
						
						
						echo '<h3><a href=/article/?id='.$article[0].'>'.$article[1].'</a></h3>';  

		}   ?>

</section>
<section>

<h2>Your Data Score</h2>
<?php 
	
	echo'<h1 id="datascore">'.$dataScore.'</h1>';
	if($dataScore ==0){
		
		
		echo '<p class="link">You haven'."'t uploaded any data yet!</p>";
		
	}
	
?>


</section>

<h1>Your Personal Data</h1>
<section>
<h2>Data about user 1</h2>
<p>Data from a sensor tagged to the user themselves will appear here</p>



</section>
<section>
<h2>Data about user N</h2>
<p>Data from a sensor tagged to the user themselves will appear here</p>



</section>
</div>
<footer>
<?php include($_SERVER['DOCUMENT_ROOT']."/footer.php");?>
</footer>
</body>
</html>