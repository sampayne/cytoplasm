<?php

require($_SERVER['DOCUMENT_ROOT'].'/api/include.php');
require($_SERVER['DOCUMENT_ROOT'].'/api/checkLogin.php');



  $articles = ArticleFactory::GetAllArticles();
  

?>
<!DOCTYPE html>

<html>
<head>
    <title>Articles | Cytoplasm</title><?php require $_SERVER['DOCUMENT_ROOT'] . '/defaults/head.php' ?>
</head>

<body>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/defaults/header/header.php'?>

    <div class="page">
        <h1>Articles</h1>

        <section>
            <h2>All Public Articles</h2><?php if(count($articles)): ?>

            <ul>
                <?php foreach($articles as $article):?>

                <li>
                    <?php echo $article->name()?> | <a href="article/?article=<?php echo $article->id()?>">View</a> 
             
                </li>
                
                       <?php endforeach ?>
            </ul><?php else: ?>

            <p>There are no public articles available</p><?php endif ?>
            </section>
    </div><?php require $_SERVER['DOCUMENT_ROOT'] . '/defaults/footer/footer.php'?>
</body>
</html>
