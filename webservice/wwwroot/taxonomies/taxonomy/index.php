<?php

require($_SERVER['DOCUMENT_ROOT'].'/api/include.php');
require($_SERVER['DOCUMENT_ROOT'].'/api/checkLogin.php');

if(isset($_GET['taxonomy'])){
   
    $taxonomy = TaxonomyFactory::LoadWithID($_GET["taxonomy"]);
    
}else{
    
    header('Location: /taxonomies/');
    die();
    
}

?>

<!DOCTYPE html>

<html>
<head>
    <title><?php echo $taxonomy->fullName()?> | Cytoplasm</title>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/defaults/head.php' ?>
</head>
<body>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/defaults/header/header.php'?>
    <div class="page">
        <h1><?php echo $taxonomy->fullName()?></h1>
        <section>
        
        
        
        </section>
    </div><?php require $_SERVER['DOCUMENT_ROOT'] . '/defaults/footer/footer.php'?>
</body>
</html>


