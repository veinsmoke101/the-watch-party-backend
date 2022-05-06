<?php

$app->router->get('/login',             [new UserController, 'login']);
$app->router->post('/login',            [new UserController, 'login']);
$app->router->get('/register',          [new UserController, 'register']);
$app->router->post('/register',         [new UserController, 'register']);
$app->router->get('/profile/{id}',      [new UserController, 'profile']);
$app->router->get('/room/{id}',         [new RoomController, 'room']);
$app->router->post('/new/room',         [new RoomController, 'newRoom']);
$app->router->get('/room/all/users',    [new RoomHistoryController, 'roomUsers']);
$app->router->get('/room/current/users',[new RoomHistoryController, 'roomUsers']);
$app->router->get('/user/rooms',        [new RoomHistoryController, 'usersRooms']);





