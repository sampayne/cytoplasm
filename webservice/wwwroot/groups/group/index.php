<?php

require($_SERVER['DOCUMENT_ROOT'].'/api/include.php');
require($_SERVER['DOCUMENT_ROOT'].'/api/checkLogin.php');


if(!isset($_GET['group']) || $_GET['group'] < 1)
{

header('Location: /group/');
die();

}

$group = GroupFactory::LoadWithID($_GET['group'],0);

?>
<!DOCTYPE html>
<html>
    <head>
    <title> <?php echo $group->fullName()?> Group | Cytoplasm</title>
	   <?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/head.php');?>    </head>
    <body>
        <?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/header/header.php');?>

        <div class="page">
        <h1><?php echo $group->fullName() ?></h1>
            <section class="column_2">
                <h2>Articles</h2>
                
                
                <p>This groups associated articles:</p>
                <ul>
                    <?php if(!is_null($group->articles())):?>
                <?php foreach($group->articles() as $article):?>
		         
                    <li>
                        <?php echo $article->name()?> | <a href="/articles/article/?article=<?php echo $article->id()?>">View</a>
                    </li>
               <?php endforeach ?>
               
               <?php else:?>
                <p>This group has no articles.</p>
               
               
                 <?php endif ?>
                </ul>
             </section>
            
            <section class="column_2">
                <h2>Sub Groups</h2>
                
                
                <?php if(!is_null($group->subgroups())):?>
                               
                <p>Sub groups of <?php echo $group->name()?>:</p>
                <ul>

                <?php foreach($group->subgroups() as $subgroup): ?>
		  
				<li><?php echo $subgroup->name() ?> | <a href="?group=<?php echo $subgroup->id()?>">View</a></li>
		         
		         <?php endforeach ?>
		                         </ul>
		                         
            <?php else:?>
            
            <p>No Subgroups</p>
		                         
                <?php endif ?>

             </section>
             
             <section class="column_2">
             <h2>Supergroup</h2>
             
             <?php if(is_null($group->super())):?>
             <p>This is the root group.</p>
             
             <?php else:?>
              <p><?php echo  $group->super()->fullName()?> | <a href="?group=<?php echo $group->super()->id()?>">View</a></p>
             <?php endif ?>
             </section>
          </div>
            <?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/footer/footer.php');?>
    </body>
</html>