<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT']."/group_manager.php");

$userID = $_SESSION['userID'];
$groupManager = new GroupManager;
$groups = $groupManager->getGroups($userID,1);

$error    =$_GET['error'];



?>

<html>
  <head><?php include($_SERVER['DOCUMENT_ROOT'].'/head.php');?>  
  <link rel="stylesheet" type="text/css" href="creategroup.css">
  </head>
  <body>
    <header>
      <?php include($_SERVER['DOCUMENT_ROOT'] . '/header.php'); ?>
    </header>
    <div id="page">
      <section>
        <form name="create_group" action="create.php" method="post">
          <?php if ($error) { echo '<h2>!!!' . $error . '!!!</h2>';} ?>
          <label>Group Name:</label><input type="text" name="name"><br/>
          <label>Parent Group:</label>
          <select name="parent">
            <option value="-1" select="selected">New Group</option>
            
            <?php
    
					foreach($groups as $group){
						
						echo '<option value='.$group[0].'>'.$group[1].'</option>';  

					}                                                         
			?>

                      </select>
          <br/>
          <input type="submit" name="Submit">
        </form>
      </section>
    </div>
    <footer>
      <?php include($_SERVER['DOCUMENT_ROOT']."/footer.php");?>
    </footer>
  </body>
</html>