<?php

	require($_SERVER['DOCUMENT_ROOT'].'/api/include.php'); 
    require($_SERVER['DOCUMENT_ROOT'].'/api/checkLogin.php'); 
    
    if($PUBLIC){
        
        //React
    }

?>
<!DOCTYPE html>
<html>
    <head>
	   <?php require($_SERVER['DOCUMENT_ROOT'].'/defaults/head.php');?>    </head>
    <body>
    <?php require($_SERVER['DOCUMENT_ROOT'].'/defaults/header/header.php');?>
        <div class="page">
            <section>
                <h2>Section Title</h2>
                <p>Some words go here</p>
                <ul>
                    <li>Example</li>
                    <li>Example</li>
                    <li>Example</li>
                    <li>Example</li>
                </ul>
                <p class="linkp"><a href="taxonomy">Click here to see all data categories</a></p>
            </section>
          </div>
            <?php require($_SERVER['DOCUMENT_ROOT'].'/defaults/footer/footer.php');?>
    </body>
</html>