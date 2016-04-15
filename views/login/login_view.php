
<h1><?php echo (Session::get("loggedIn") == "true") ? 'Welcome ' . ucwords(Session::get("user")) : 'Login to enter'; ?></h1>

<p>You're looking at views/login/login_view.php </p>

<form action="<?php echo URL; ?>login/run" method="post">
    <label>Login</label><input type="text" name="login" /> <br>
    <label>Password</label><input type="password" name="password" /><br>
    <label></label><input type="submit">
</form>


<div>
    <?php
    if(Session::get("user")!== null ) {
        $client = Session::get("user");
        $client_path = getcwd() . "/views/client/" . $client . "/login_append.php";
        if (file_exists($client_path)) {
            include $client_path;
        }
    }
    ?>
</div>
