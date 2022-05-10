<?php

use app\controllers\RoomController;
use app\controllers\UserController;
use app\controllers\RoomHistoryController;

$app->router->get('/', function () {
    echo date('Y-m-d H:i:s');
    die();
}   );

$app->router->get('/login',                 [new UserController, 'login']);
$app->router->post('/login',                [new UserController, 'login']);
$app->router->get('/register',              [new UserController, 'register']);
$app->router->post('/register',             [new UserController, 'register']);
$app->router->get('/profile/{id}',          [new UserController, 'profile']);
$app->router->get('/room/{id}',             [new RoomController, 'room']);
$app->router->post('/new/room',             [new RoomController, 'newRoom']);
$app->router->post('/room/all/users',       [new RoomHistoryController, 'roomUsers']);
$app->router->post('/room/current/users',   [new RoomHistoryController, 'currentRoomUsers']);
$app->router->post('/user/rooms',           [new RoomHistoryController, 'usersRooms']);

