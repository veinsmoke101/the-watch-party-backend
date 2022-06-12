<?php

namespace app\controllers;

use app\core\Controller;
use app\models\RoomHistory;

class RoomHistoryController extends Controller
{

    private RoomHistory $roomHistory;


    public function __construct()
    {
        parent::__construct();
        $this->roomHistory = new RoomHistory();
    }


    public function roomUsers()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $room_id = $data['room_id'];

//        $RoomHistory = $this->model('RoomHistory');
//        $users = $RoomHistory->getRoomUsers($room_id);
        $users = $this->roomHistory->getRoomUsers($room_id);
        if(count($users) === 0){
            echo "The room of Id = $room_id does not exist";
        }else {
            echo json_encode($users);
        }
    }


    public function currentRoomUsers()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $roomRef = $data['room_ref'];

        $Room = $this->model('Room');
        $room = $Room->getRoomByRef($roomRef);

        $RoomHistory = $this->model('RoomHistory');
        $users = $RoomHistory->getRoomCurrentUsers($room['id']);
        if(count($users) === 0){
            $response = [
                'status'=> 'error',
                'message'=> 'The room may have been expired or never existed'
            ];
        }else{
            $response = [
                'status'=> 'success',
                'data'=> $data['count'] ? count($users) : $users
            ];
        }
        echo json_encode($response);
    }

    public function userRooms()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $user_id = $data['user_id'];

        $RoomHistory = $this->model('RoomHistory');
        $rooms = $RoomHistory->getUserRooms($user_id);
        if(count($rooms) === 0){
            echo "This have never joined a room or may never existed!";
        }else {
            echo json_encode($rooms);
        }
    }
}