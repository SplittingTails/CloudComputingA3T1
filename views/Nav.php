<?php
/***** HEADER OF WEBSITE ******/
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//change page title based on active page
($pageTitle == "HomePage") ? $Home = "class=\"active\"" : $Home = "";
($pageTitle == "Register") ? $Register = "class=\"active\"" : $Register = "";
($pageTitle == "Mainpage") ? $Mainpage = "class=\"active\"" : $Mainpage = "";

if (!isset($_SESSION['user'])) {
    ?>
    <nav class="navbar navbar-expand-xl bg-body-tertiary">
        <div class="container text-center">
            <a class="navbar-brand" href="/">Reliable Parking</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/register">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
<?php } else if (isset($_SESSION['user'])) { ?>
        <nav class="navbar navbar-expand-xl bg-body-tertiary">
            <div class="container text-center">
                <a class="navbar-brand" href="/mainpage">Reliable Parking</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="/mainpage">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="/profile">User profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="/logout">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
            <h3 class="ml-5"><?php echo $_SESSION['user']['email'] ?></h3>
        </nav>
<?php } ?>