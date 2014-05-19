<?php



require($_SERVER['DOCUMENT_ROOT'].'/api/include.php');
require($_SERVER['DOCUMENT_ROOT'].'/api/checkLogin.php');


$groups = GroupFactory::LoadAllGroups();

?>
<!DOCTYPE html>
<html>
    <head>
	   <?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/head.php');?>   
       <title>Groups | Cytoplasm</title>
    </head>
    <body>
        <?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/header/header.php');?>

        <div class="page">
        <h1>Full Group List</h1>
            <section>
                <h2>Groups</h2>
                <ul>
                <?php foreach($groups as $group):?>
		         
                    <li>
                        <?php echo $group->fullNameReversed()?> | <a href="group/?group=<?php echo $group->id()?>">View</a>
                    </li>
               <?php endforeach ?>
                 
                </ul>
             </section>
           
          </div>
            <?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/footer/footer.php');?>
    </body>
</html>