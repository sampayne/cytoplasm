<?php

require($_SERVER['DOCUMENT_ROOT'].'/api/include.php');
require($_SERVER['DOCUMENT_ROOT'].'/api/checkLogin.php');


  if(!isset($_GET['article'])){
      
      header('Location: /articles/');
      die();
      
  }

  $article = ArticleFactory::LoadWithID($_GET['article']);
  $article->loadAllDataStreams(1);
  

?>
<!DOCTYPE html>

<html>
<head>
    <title><?php echo $article->name()?> | Cytoplasm</title>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/defaults/head.php' ?>
</head>

<body>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/defaults/header/header.php'?>

    <div class="page">
        <h1>Article - <?php echo $article->name() ?></h1>

        <section>
                        
           <?php if(is_null($article->userCreator())):?>
           
           <h2>Group Owner</h2>


           <p><?php echo $article->groupCreator()->fullName() ?> | <a href="/groups/group/?group=<?php echo $article->groupCreator()->id()?>">View</a></p>

           <?php else: ?>
            <h2>User Owner</h2>
            <p>User - <?php echo $article->userCreator()->name()?> | <a href="/users/user/?user=<?php echo $article->userCreator()->id()?>">View</a></p>
        <?php endif ?>
        </section>
        
        <section>
        <h2>Details</h2>
        <p>Description: <?php echo $article->description()?>     </p>        
        
        <?php if(!is_null($article->additional())):?>
        <p>Additional Details:   </p> 
        <?php foreach( explode('/', $article->additional()) as $field):?>  
            
            <p> <?php echo $field?> </p>
            
        <?php  endforeach ?>
        <?php endif ?>
        
        </section>
        
    <section>
    <h2>Available Data Categories</h2>
    
    <?php if(!is_null($article->dataStreams())):?>
    
    <?php foreach($article->dataStreams() as $stream):?>
    
    <p><?php echo $stream->taxonomy()->fullName() ?> (<?php echo $stream->dataCount() ?>) | <a href="/data/?article=<?php echo $article->id()?>&taxonomy=<?php echo $stream->taxonomy()->id()?>">View</a>
    <?php endforeach ?>
    
    
    <?php else:?>
    
    <p>No Data Available</p>
    <?php endif ?>
    
    
    
    </section>
         
        
        
    </div><?php require $_SERVER['DOCUMENT_ROOT'] . '/defaults/footer/footer.php'?>
</body>
</html>
