<?php
  session_start();
  if (!isset($_SESSION['userID'])) {
      session_destroy();
  }
  include_once $_SERVER['DOCUMENT_ROOT'] . "/article_manager.php";
  $userID         = $_SESSION['userID'];
  $articleID      = $_GET['id'];
  $articleManager = new ArticleManager;
  $permission     = -1;
  if ($articleID > 0) {
      $valid = $articleManager->checkUserValid($userID, $articleID);
      if ($valid > 0) {
          $permission = 1;
      } else {
          $permission = -1;
      }
  }
  ?>
<!DOCTYPE html>
<html>
  <head>
    <?php
      include $_SERVER['DOCUMENT_ROOT'] . '/head.php';
      ?>    
  </head>
  <body>
    <header>
      <?php
        include $_SERVER['DOCUMENT_ROOT'] . '/header.php';
        ?>
    </header>
    <div id="page">
      <?php
        if ($permission > 0) {
            $article = $articleManager->getFullDetails($articleID);
            if ($article == -1) {
                echo 'there was an error';
            } else {
                echo '<h1>' , $article['name'] , '</h1>';
                echo '<section>';
                echo '<h2>About</h2>';
                echo '<p>' , $article['description'] , '</p>';
                
         
                
                $additional = explode('/',$article['additional_fields']);
                
                foreach($additional as $string){
	                
	                echo '<p>',$string,'</p>';
	                
                }
                
                
                echo '<p>Some other information about the article will go here</p>';
            }
        } else {
            echo '<h2>You Do Not Have Permission to View This Article</h2>';
        }
        ?>
      </section>
      <section>
        <h2>Data 1</h2>
        <p>This section will contain some data about this article from sensor 1</p>
      </section>
      <section>
        <h2>Data 2</h2>
        <p>This section will contain some data about this article from sensor 2</p>
      </section>
      <section>
        <h2>Data N</h2>
        <p>This section will contain some data about this article from sensor N</p>
      </section>
    </div>
    <footer>
      <?php
        include $_SERVER['DOCUMENT_ROOT'] . '/footer.php';
        ?>
    </footer>
  </body>
</html>