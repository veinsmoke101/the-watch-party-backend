<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use DateTime;
use Exception;

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

        $Room = $this->model('Room');
        if($Room->insert($newRoom)){
            echo 'Room created successfully';
        }else{
            echo 'Something went wrong';
        }
    }

    /**
     *
     * @throws Exception
     */
    public function room()
    {
        $params = Application::$app->request->getRouteParams();
        $roomId = $params["id"];
        $Room = $this->model('Room');

        $roomData = $Room->getRoomById($roomId);
        if($roomData["expire_at"]){
            $start_at   = new DateTime($roomData["start_at"]);
            $expire_at  = new DateTime($roomData["expire_at"]);
            if($start_at < $expire_at){
                $roomData = json_encode($roomData);
                echo $roomData;
            }else{
                $this->response->setStatusCode(410);
                echo json_encode('this room is expired');
            }
        }
    }
}