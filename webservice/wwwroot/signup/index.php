<?php

    require($_SERVER['DOCUMENT_ROOT'].'/api/include.php');

    if(isset($_POST['commit']) && $_POST['commit'] == 1){
    
     $errors  = UserFactory::Create($_POST['username'], $_POST['password'], $_POST['name']);
    
     if(!isset($errors)){   
        
         session_start();
    session_unset();
    session_destroy();

        $password = hash('sha512', $_POST['password']);

        $CURRENT_USER =  LoginFactory::login($_POST['username'],$password);
        
        if(!isset($CURRENT_USER->error)){
       
            session_start();
		    $_SESSION['user'] = serialize($CURRENT_USER);
            header('Location: /dashboard');	
            die();
    
            }

     
     }
          
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
            <h1>Sign Up</h1>
            <section>
                        <?php 
                        if(isset($errors)):
                        foreach($errors as $error): ?>
                        
                        <p class="error"><?php echo $error->error ?></p>
                        
                        <?php endforeach; endif ?>
                        
                        <?php echo isset($CURRENT_USER->error) ? '<p class="error">'.$error->error.'</p>' : '' ?>

                        <form method="post" action="/signup/">
                        <input type="hidden" value="1" name="commit">
                        <input type="text"     name="name"     placeholder="Name..."     required value="<?php echo isset($_POST['name']) ? $_POST['name'] : '' ?>">
                        <input type="text"     name="username"     placeholder="Username..."     required value="<?php echo isset($_POST['username']) ? $_POST['username'] : '' ?>">
                        <input type="password"     name="password"     placeholder="Password..."     required value="<?php echo isset($_POST['password']) ? $_POST['password'] : '' ?>">
                        <button type="submit">Create</button>
                    </form>
                
            </section>
        </div>
        <?php include($_SERVER['DOCUMENT_ROOT'].'/defaults/footer/footer.php');?>
    </body>
</html>