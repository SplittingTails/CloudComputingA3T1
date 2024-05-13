<?php

require ('helper\helper.php');

$uri = @parse_url($_SERVER['REQUEST_URI'])['path'];

$routes = [
    '/' => __DIR__ . '/views/homepage.php',
    '' => __DIR__ . '/views/homepage.php',
    '/register' => __DIR__ . '/views/register.php',
    '/mainpage' => __DIR__ . '/views/mainpage.php',
    '/post-validation' => __DIR__ . '/helper/Post-validation.php',
    '/logout' => __DIR__ . '/helper/logout.php',
    '/parkingspot' => __DIR__ . '/views/parkingspot.php',
    '/profile' => __DIR__ . '/views/profile.php',
];

function routeto($uri, $routes)
{

    if (array_key_exists($uri, $routes)) {
        require $routes[$uri];
    } else {
        error();
    }
}

function error($code = 404)
{
    http_response_code(404);
    require __DIR__ . '/views/404.php';
    die();
}

routeto($uri, $routes);

#Refference