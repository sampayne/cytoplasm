<?php

require($_SERVER['DOCUMENT_ROOT'].'/api/include.php');

$users = UserFactory::GetAllUsers();
    
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/head.php')?>
        <title>Users | Cytoplasm</title>
    </head>
    <body>
        <?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/header/header.php')?>
        <div class="page">
            <h1>Users</h1>
            <section>
            <?php if(isset($users)):?>
            <ul>    
                <?php foreach($users as $user):?>
                <li><?php echo $user->name()?> - <?php echo $user->username()?> | <a href="/users/user/?user=<?php echo $user->id()?>">View</a></li>
                <?php endforeach ?>
            </ul>
            <?php endif ?>
            </section>
        </div>
        <?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/footer/footer.php')?>
    </body>
</html>