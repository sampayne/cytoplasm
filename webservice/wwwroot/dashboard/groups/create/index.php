<?php

require($_SERVER['DOCUMENT_ROOT'].'/api/include.php');
require($_SERVER['DOCUMENT_ROOT'].'/api/checkLogin.php');

if($PUBLIC){
    
    header('Location: /login');
    die();
}


if(isset($_POST['commit']) && $_POST['commit'] == 1){
     
 //Attempt creation
 
 header('Location: /dashboard/groups/?notification=Group+Created');
 die();
      
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

<h1>Create New User</h1>
<section>

<section class="subsection">

<!-- PRINT ERRORS HERE -->

<form>
    <input type="hidden" value="1" name="commit">
    <input type="text"     name="name"     placeholder="Name..."     required value="<?php echo isset($_POST['name']) ? $_POST['name'] : '' ?> ">
<select name="group">
<option value="-1" selected disabled>Super Group...</option>
<?php foreach($CURRENT_USER->adminGroups() as $group):?>
<option value="<?php echo $group->id()?>" <?php echo isset($_POST['group']) && $_POST['group'] == $group->id() ? 'selected' : ''?>><?php echo $group->name() ?></option>
<?php endforeach ?>
</select>
<button type="submit">Create</button>
</form>
</section>
</section>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/footer/footer.php');?>

</body>
</html>

