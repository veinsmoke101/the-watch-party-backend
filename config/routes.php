<?php

use app\controllers\MessageController;
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
$app->router->get('/room/{room_id}/{user_id}',[new RoomController, 'joinRoom']); // done
$app->router->post('/leave/room',           [new RoomController, 'leaveRoom']); // done
$app->router->post('/new/room',             [new RoomController, 'newRoom']); // done
$app->router->post('/new/vid',              [new RoomController, 'newVideo']); // done
$app->router->post('/room/all/users',       [new RoomHistoryController, 'roomUsers']); // done
$app->router->post('/room/current/users',   [new RoomHistoryController, 'currentRoomUsers']); // done
$app->router->post('/user/rooms',           [new RoomHistoryController, 'userRooms']); // done
$app->router->post('/new/message',          [new MessageController(), 'newMessage']); // done
$app->router->post('/last/messages',        [new MessageController(), 'lastMessages']);
$app->router->post('/video/pause',          [new MessageController(), 'pauseVideoMessage']);
$app->router->post('/video/play',           [new MessageController(), 'playVideoMessage']);
$app->router->post('/video/jump',           [new MessageController(), 'jumpVideoMessage']);

