<?php

namespace app\controllers;

use app\core\Controller;

class RoomController extends Controller
{

    public function newRoom()
    {
        $roomId = uniqid('room-');

        $start_at = ( isset($_POST['start_at']) && !empty($_POST['start_at']) ) ? $_POST['start_at'] : date('Y-m-d H:i:s');
        $expire_at = ( isset($_POST['expire_at']) && !empty($_POST['expire_at']) ) ? $_POST['expire_at'] : null;


        $newRoom = array(
            "title"             => $_POST['title'],
            "created_at"        => date('Y-m-d H:i:s'),
            "start_at"          => $start_at,
            "expire_at"         => $expire_at,
            "author"            => $_POST['author'],
            "unique_reference"  => $roomId
        );

        $room = $this->model('Room');
        if($room->insert($newRoom)){
            echo 'Room created successfully';
        }else{
            echo 'Something went wrong';
        }
    }

}