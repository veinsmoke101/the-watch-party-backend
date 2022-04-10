<?php

require_once __DIR__ . '/../vendor/autoload.php';
const URL_PATH = "http://localhost/CineMaster";


use app\core\Application;
use app\controllers\PostController;
use app\controllers\UserController;
use app\controllers\SiteController;
use app\core\Request;
use app\core\Router;

$app = new Application(dirname(__DIR__));


$app->router->get('/register',  [new UserController(), 'register']);
$app->router->post('/register', [new UserController(), 'register']);
$app->router->get('/login',     [new UserController(), 'login']);
$app->router->post('/login',    [new UserController(), 'login']);
$app->router->get('/logout',    [new UserController(), 'logout']);
$app->router->get('/post',      [new PostController(), 'post']);
$app->router->post('/post',      [new PostController(), 'post']);
$app->router->post('/add-post', [new PostController(), 'addPost']);
$app->router->get('/',          [new SiteController(), 'home']);
$app->router->get('/home',      [new SiteController(), 'home']);

$app->run();