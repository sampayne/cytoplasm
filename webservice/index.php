<?php
session_start();
if (!isset($_SESSION['userID'])) {
    session_destroy();
}
?>
<!DOCTYPE html>
<html>
    <head>
	   <?php include($_SERVER['DOCUMENT_ROOT'].'/head.php');?>    </head>
    <body>
        <header>
            <?php include("header.php");?>
        </header>
        <div id="page">
            <section>
                <h2>Top Taxonomies</h2>
                <p>The most popular data categories</p>
                <ol>
                    <li>Example</li>
                    <li>Example</li>
                    <li>Example</li>
                    <li>Example</li>
                </ol>
                <p class="linkp"><a href="taxonomy">Click here to see all data categories</a></p>
            </section>
            <section>
                <h2>Top Contributes</h2>
                <p>The sites top contributors!</p>
                <ol>
                    <li>Example Group</li>
                    <li>Example User</li>
                    <li>Example User</li>
                    <li>Example Group</li>
                    <li>Example User</li>
                </ol>
                <p class="linkp"><a href="contributer">Click here to see all our public contributors</a></p>
            </section>
            <section>
                <h2>Help Contribute!</h2>
                <p>Help contribute to our data banks! Here are some of the categories that need the most help!
	               

                </p> <ol>
                    <li>Example</li>
                    <li>Example</li>
                    <li>Example</li>
                    <li>Example</li>
                    <li>Example</li>
                </ol>
                                <?php if(isset($_SESSION['userID'])){
                    echo '<p class="linkp"><a href="dashboard">Visit Your Dashboard to Contribute</a></p>';
                    
                    }else{
                    
                    echo '<p class="linkp"><a href="login">Sign in or Sign Up to Contribute</a></p>';
                    
                    }?>
            </section>
            <section>
                <h2>How it Works</h2>
                <p>Cytoplasm is a free-to-use data storage system</p>
                <p class="linkp"><a href="about">Click here</a> to find out more about how the system works</p>
            </section>
        </div>
        <footer>
            <?php include("footer.php");?>
        </footer>
    </body>
</html>