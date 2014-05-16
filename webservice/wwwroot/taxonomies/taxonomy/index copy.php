<?php

require($_SERVER['DOCUMENT_ROOT'].'/api/include.php');
require($_SERVER['DOCUMENT_ROOT'].'/api/checkLogin.php');


if(isset($_GET['taxonomy'])){
   
   $taxonomy = TaxonomyFactory::LoadWithID($_GET["taxonomy"]);
    
}else{
    
    
 $allTaxonomies = TaxonomyFactory::LoadAllTaxonomies();
    
}




?>