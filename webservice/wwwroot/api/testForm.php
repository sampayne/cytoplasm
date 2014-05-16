<form method="post">
    <input type="hidden" name="commit" value="1">
    <input type="text" name="start" value="<?php echo time(); ?>">
    <input type="text" name="end" value="<?php echo time()+60?>">
    <input type="text" name="article" value="41">
    <input type="text" name="username" value="root">
    <input type="text" name="password" value="password"
    >
    <select name="api">
        <option value="0">LOGIN</option>
        <option value="1">ARTICLES</option>
        <option value="2">DATA</option>
    </select>
    <input type="text" name="authkey" value="4b5796e1b4a7bf236480a35ff5ac978da4a9b9a01ad1286bd9d97afe816bd0504d1cbeb52d827478b6d99a350fa37dd1a34121d2351800ca2c813207990a3948">
    <input type="text" name="taxonomy" value="health-cardio-heartrate">
    <input type="submit" value="submit">
</form>


<?php

if (isset($_POST['commit'])) {
    
    $_POST['password'] = hash('sha512', $_POST['password']);
    
    if ($_POST['api'] == 0) {
        
        include('login/index.php');
        
    } else if ($_POST['api'] == 1) {
        
        include('articles/index.php');
        
    } else if ($_POST['api'] == 2) {
        
        include('data/index.php');

    } 
}
?>
