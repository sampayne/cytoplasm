<?php
session_start();
if (!isset($_SESSION['userID'])) {
    session_destroy();
}

$groupID = $_GET['id'];
include_once($_SERVER['DOCUMENT_ROOT'].'/group_manager.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/article_manager.php');

$groupManager = new GroupManager;
$articleManager = new ArticleManager;
$group = $groupManager->getGroup($groupID);
$subgroups = $groupManager->findSubgroups($groupID,$group['name']);
$articles = $articleManager->getArticlesByGroup($groupID);

?>
<!DOCTYPE html>
<html>
    <head>
	   <?php include($_SERVER['DOCUMENT_ROOT'].'/head.php');?>    </head>
    <body>
        <header>
            <?php include($_SERVER['DOCUMENT_ROOT']."/header.php");?>
        </header>
        <div id="page">
        <h1><?php echo $group['name'];?></h1>
            <section>
                <h2>Articles</h2>
                
                
                <p>This groups associated articles:</p>
                <ol>
                <?php 
	                
	                foreach($articles as $article){
		         
						echo '<li>'.$article[1].' | <a href="/article/?id='.$article[0].'">View</a></li>';
		         
		         
	                }
	              
                ?>
                 
                </ol>
             </section>
                         <section>
                <h2>Sub Groups</h2>
                
                
                <p>Sub groups of <?php echo $group['name'];?>:</p>
                <ol>
                <?php 
	                
	                foreach($subgroups as $subgroup){
		         
						echo '<li>'.$subgroup[1].' | <a href="/group/?id='.$subgroup[0].'">View</a></li>';
		         
		         
	                }
	              
                ?>
                 
                </ol>
             </section>
          </div>
        <footer>
            <?php include($_SERVER['DOCUMENT_ROOT']."/footer.php");?>
        </footer>
    </body>
</html>