<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\models\Room;
use DateTime;
use Exception;

class RoomController extends Controller
{


    public function newRoom()
    {
        $roomId = uniqid('room_');

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
        // get Room id from the url
        $params = Application::$app->request->getRouteParams();
        $roomId = $params["id"];

        // get user_data of the request sender
        $json = file_get_contents('php://input');
        $userData = json_decode($json, true);

        // call models
        $Room = $this->model('Room');
        $RoomHistory = $this->model('RoomHistory');

        // handle the <<joinRoom>> process
        $roomData = $Room->getRoomByRef($roomId);
        $start_at   = new DateTime(date('Y-m-d H:i:s'));
        $expire_at  = new DateTime($roomData["expire_at"]);

        //check if room is expired
        if($roomData["expire_at"] && $start_at >= $expire_at) {
            $this->response->setStatusCode(410);
            $response = array(
                'status' => 'error',
                'message' => "room expired"
            );
            echo json_encode($response);
            return;
        }

        $roomHistoryData = array(
            'room_id' => $roomData['id'],
            'user_id' => $userData['id'],
            'user_joined_at' => date('Y-m-d H:i:s')
        );
        if(!$RoomHistory->insert($roomHistoryData)){
            $response = array(
                'status' => 'error',
                'message' => "Something went wrong, can't join this room"
            );
            echo json_encode($response);
            return;
        }
        $response = array(
            'status' => 'success',
            'data' => $roomData,
            'message' => "joined $roomId successfully"
        );
        $response = json_encode($response);
        echo $response;
    }

}