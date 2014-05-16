<?php

require($_SERVER['DOCUMENT_ROOT'].'/api/include.php');
require($_SERVER['DOCUMENT_ROOT'].'/api/checkLogin.php');

if($PUBLIC){
    
    header('Location: /login');
}



?>

<!DOCTYPE html>
<html>
<head>
<?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/head.php');?>  
</head>
<body>
<?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/header/header.php');?>
<div class="page">
<h1>Welcome <?php echo $user->name()?></h1>


<h2>User Dashboard</h2>

<section>
<h2>Actions</h2>
</section>

<section>
<h2>Your Groups</h2>
<p>You are part of the following groups:</p>
<?php foreach($user->groups(0) as $group):?>

<p> <?php echo $group->fullName() ?> | <a href="/groups/group/?group=<?php echo $group->id() ?>">View</a></p>


<?php endforeach ?>

</section>

<section>
<h2>Your Group Articles</h2>
<p>You have access to the following articles:</p>
<?php foreach($user->groupArticles(0) as $article):?>

<p> <?php echo $article->name() ?> | <a href="/articles/article/?article=<?php echo $article->id() ?>">View</a></p>


<?php endforeach ?>

</section>
<?php if(!is_null($user->adminGroups(0)) || !is_null($user->userArticles())): ?>

    <h2>Admin Dashboard</h2>
    
    
    <?php if(!is_null($user->adminGroups(0))):?>
    
    <section>
<h2>Your Admin Groups</h2>
<p>You have admin access to the following groups:</p>
<?php foreach($user->adminGroups(0) as $group):?>

<p> <?php echo $group->name() ?> | <a href="/groups/group/?group=<?php echo $group->id() ?>">View</a> | <a href="group/?action=edit&class=<?php echo $group->id()?>">Edit</a>

| <a href="group/?action=delete&class=<?php echo $group->id()?>">Delete</a>
</p>


<?php endforeach ?>

</section>
    
    
    <?php endif ?>
    
<?php if(!is_null($user->userArticles(0))):?>

      <section>
<h2>Your Articles</h2>
<p>You have admin access to the following articles:</p>
<?php foreach($user->userArticles(0) as $article):?>

<p> <?php echo $article->name() ?> | <a href="/articles/article/?article=<?php echo $article->id() ?>">View</a> | <a href="article/?action=edit&class=<?php echo $article->id()?>">Edit</a>

| <a href="article/?action=delete&class=<?php echo $article->id()?>">Delete</a>
</p>


<?php endforeach ?>

</section>
    



<?php endif ?>


<?php if(!is_null($user->adminArticles(0))):?>

      <section>
<h2>Your Admin Articles</h2>
<p>You have admin access to the following articles:</p>
<?php foreach($user->adminArticles(0) as $article):?>

<p> <?php echo $article->name() ?> | <a href="/articles/article/?article=<?php echo $article->id() ?>">View</a> | <a href="article/?action=edit&class=<?php echo $article->id()?>">Edit</a>

| <a href="article/?action=delete&class=<?php echo $article->id()?>">Delete</a>

</p>


<?php endforeach ?>

</section>
    



<?php endif ?>


<?php endif ?>


</div>

<?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/footer/footer.php');?>

</body>
</html>