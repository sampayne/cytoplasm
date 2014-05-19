<?php

require($_SERVER['DOCUMENT_ROOT'].'/api/include.php');

$taxonomies = TaxonomyFactory::GetAllTaxonomies();
    
?>
<!DOCTYPE html>
<html>
    <head>
	   <?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/head.php')?>
       <title>Taxonomies | Cytoplasm</title>
    </head>
    <body>
            <?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/header/header.php')?>
    
        <div class="page">
            <h1>Taxonomies</h1>
            <section>
            <?php if(isset($taxonomies)):?>
                <ul>
                <?php foreach($taxonomies as $taxonomy):?>  
                    <li><?php echo $taxonomy->fullName()?> | <a href="taxonomy/?taxonomy=<?php echo $taxonomy->id() ?>">View</a></li>
                <?php endforeach ?>
                </ul>
            <?php endif ?>
            </section>
        </div>
     <?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/footer/footer.php')?>    </body>
</html>