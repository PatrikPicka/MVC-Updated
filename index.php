<?php
session_start();

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
require_once(ROOT . DS . 'config' . DS . 'config.php');
require(ROOT . DS . "vendor" . DS . "autoload.php");
//require_once(ROOT . DS . 'config' . DS . 'bootstrap.php');
//require_once(ROOT . DS . 'app' . DS . 'lib' . DS . 'helpers' . DS . 'functions.php');


use \Core\Router;


//Autoloader
spl_autoload_register(function ($className) {
    $parts = explode("\\", $className);
    $class = end($parts);
    array_pop($parts);
    $path = strtolower(implode(DS, $parts));
    $path = ROOT . DS . $path . DS . $class . ".php";
    if (file_exists($path)) {
        require_once $path;
    }
});

$url = isset($_SERVER['PATH_INFO']) ? explode('/', ltrim($_SERVER['PATH_INFO'], '/')) : [];

/*
//list of pages which are accessable only when user is logged in
$accessableOnlyWhenLoggedIn = ["products"];

$user = new User();
//check if the user has to be logged in to access the page
foreach ($accessableOnlyWhenLoggedIn as $page) {
    if (isset($url[0])) {
        if ($url[0] === $page && !$user->isLoggedIn()) {
            Router::redirect("login");
        }
    }
}


*/
Router::route($url);
