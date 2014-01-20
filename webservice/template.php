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
            <?php include($_SERVER['DOCUMENT_ROOT']."/header.php");?>
        </header>
        <div id="page">
            <section>
                <h2>Section Title</h2>
                <p>Some words go here</p>
                <ol>
                    <li>Example</li>
                    <li>Example</li>
                    <li>Example</li>
                    <li>Example</li>
                </ol>
                <p class="linkp"><a href="taxonomy">Click here to see all data categories</a></p>
            </section>
          </div>
        <footer>
            <?php include($_SERVER['DOCUMENT_ROOT']."/footer.php");?>
        </footer>
    </body>
</html>