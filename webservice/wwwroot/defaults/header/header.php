<header>
    <div class="login">
        <?php if(isset($_SESSION['user'])): ?>
                <?php $user = unserialize($_SESSION['user']); ?>
                <p>Welcome <?php echo $user->name()?></p>
                <p><a href="/dashboard/">Dashboard</a> | <a href="/logout/">Logout</a></p>
        <?php else: ?>
           <p class="welcome">Welcome</p>
           <p><a href="/login/">Login</a> | <a href="/signup">Sign Up</a></p>
        <?php endif ?>
    </div>
    <img height=100 class= "logo" src="/images/logo.jpg">
    <h2>Wearable Sensor Network</h2>
    <nav>
        <ul>
        <li><a href="/articles/">Articles</a></li>
        <li><a href="/groups/">Groups</a></li>
        <li><a href="/users/">Users</a></li>
        <li><a href="/taxonomies/">Taxonomies</a></li>
        
        </ul>
    </nav>
</header>