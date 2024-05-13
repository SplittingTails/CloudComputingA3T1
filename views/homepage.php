<?php
set_time_limit(500);
include ("header.php");
require ("seed\seed.php");
seed();
?>

    <div class="w-50 position-absolute top-50 start-50 translate-middle">
        <h2>Login</h2>
        <form class="p-4 border border-black rounded" action="/post-validation" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" name='email' id="email" aria-describedby="emailHelp">
                <?php if (isset($_SESSION['alerts']['email_error']))
                    echo '<div id="emailHelp" class="form-text text-danger">' . $_SESSION['alerts']['email_error'] . '</div></p>'; ?>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name='password' id="password">
                <?php if (isset($_SESSION['alerts']['password_error']))
                    echo '<div id="passwordHelp" class="form-text text-danger">' . $_SESSION['alerts']['password_error'] . '</div></p>'; ?>
            </div>
            <?php if (isset($_SESSION['alerts']['Login_Error']))
                echo '<div id="passwordHelp" class="form-text text-danger">' . $_SESSION['alerts']['Login_Error'] . '</div></p>'; ?>


            <button type="submit" name="login" value='login' class="btn btn-primary">Submit</button>
            <a class="btn btn-primary" href="/register" role="button">Register</a>
        </form>
    </div>


<?php include ("footer.php"); ?>