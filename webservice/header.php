
<div id="title">
<h1><a id="home" href="/">Cytoplasm</a></h1>
<h2>Sensor System</h2>
</div>

<?php

echo '<div id="login">';
if(isset($_SESSION['name'])) {
    echo '<h3>Welcome ' . $_SESSION['name'] . '</h3>';
    echo '<h3><a href="/dashboard">Dashboard</a> | <a href="/logout">Logout</a></h3>';
} else {
    echo '<h3 id="welcome">Welcome</h3>';
    echo '<h3><a href="/login">Login</a> | <a href="/signup">Sign Up</a></h3>';
}

echo '</div>';
?>