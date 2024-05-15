<?php

#require ('helper\helper.php');

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

/**
 * This is an example of a front controller for a flat file PHP site. Using a
 * Static list provides security against URL injection by default. See README.md
 * for more examples.
 */
# [START gae_simple_front_controller]

/*switch (@parse_url($_SERVER['REQUEST_URI'])['path']) {
    case '/':
        require 'views/homepage.php';
        break;
    case '':
        require 'views/homepage.php';
        break;
    case '/register':
        require 'views/register.php';
        break;
    case '/post-validation':
        require 'helper/Post-validation.php';
        break;
    case '/mainpage':
        require 'views/mainpage.php';
        break;
    case '/parkingspot':
        require 'views/parkingspot.php';
        break;
    case '/logout':
        require 'helper/logout.php';
        break;
    case '/profile':
        require 'views/profile.php';
        break;
    default:
        http_response_code(404);
        exit('Not Found');
}*/


# [END gae_simple_front_controller]