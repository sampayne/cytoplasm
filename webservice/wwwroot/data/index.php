<?php

require($_SERVER['DOCUMENT_ROOT'].'/api/include.php');
require($_SERVER['DOCUMENT_ROOT'].'/api/checkLogin.php');


    $article = ArticleFactory::LoadWithID($_GET['article']);
    $taxonomy = TaxonomyFactory::LoadWithID($_GET['taxonomy']);
    
    $page = isset($_GET['page'])? $_GET['page'] : 1;
    
    $data = DataStreamFactory::LoadDataStreamForPage($article,$taxonomy,$page);

?>
<!DOCTYPE html>
<html>
    <head>
    <title><?php echo $article->name() ?> - <?php echo $taxonomy->name()?> Data - Page <?php echo $page ?></title>
	   <?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/head.php')?>    </head>
    <body>

            <?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/header/header.php')?>

        <div class="page">
        <h1><?php echo $article->name() ?> - <?php echo $taxonomy->name()?> Data - Page <?php echo $page ?></h1>
            
             <section class="pages">
                <?php for($i = 1; $i<=($data->datacount()/500)+1;$i++  ):?>
                    <a href="?article=<?php echo $_GET['article']?>&taxonomy=<?php echo $_GET['taxonomy']?>&page=<?php echo $i?>"><?php echo $i?></a>
                <?php endfor ?>
            </section>
            
            <section>
                <ul>
                <?php foreach($data->data() as $entry): ?>
                
                <li><?php echo $entry->id() ?> - <?php echo  $entry->reading_date() ?> -<?php echo date("m/d/y - H:i:s", $entry->reading_date()) ?> - <?php echo $entry->entryValues()?></li>
                
                <?php endforeach ?>
                </ul>
                
                
      
           
            </section>
            
            <section class="pages">
                <?php for($i = 1; $i<=($data->datacount()/500)+1;$i++  ):?>
                    <a href="?article=<?php echo $_GET['article']?>&taxonomy=<?php echo $_GET['taxonomy']?>&page=<?php echo $i?>"><?php echo $i?></a>
                <?php endfor ?>
            </section>
          </div>

            <?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/footer/footer.php') ?>

    </body>
</html>