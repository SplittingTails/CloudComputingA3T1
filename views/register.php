<?php
$pageTitle = 'Register';
include ("header.php");
?>

<div class="w-50 position-absolute top-50 start-50 translate-middle">
    <h2>Register</h2>
    <form class="p-4 border border-black rounded" action="/post-validation" method="post">
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" name="email" id="email" aria-describedby="emailHelp">
            <?php if (isset($_SESSION['alerts']['email_error']))
                echo '<div id="emailHelp" class="form-text text-danger">'. $_SESSION['alerts']['email_error'] .'</div></p>'; ?>
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="input" class="form-control" name="username" id="username" aria-describedby="usernameHelp">
            <?php if (isset($_SESSION['alerts']['username_error']))
                echo '<div id="usernameHelp" class="form-text text-danger">'. $_SESSION['alerts']['username_error'] .'</div></p>'; ?>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="password" aria-describedby="passwordHelp">
            <?php if (isset($_SESSION['alerts']['password_error']))
                echo '<div id="passwordHelp" class="form-text text-danger">'. $_SESSION['alerts']['password_error'] .'</div></p>'; ?>

        </div>

        <button type="submit" name="register" value='register' id="register" class="btn btn-primary">Submit</button>
    </form>
</div>

<?php include ("footer.php"); ?>