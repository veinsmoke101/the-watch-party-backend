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


    public function joinRoom()
    {
        $json = file_get_contents('php://input');
        $userData = json_decode($json);

        if($this->roomHistory->insert($userData)){
            echo 'Successfully joined the room';
        }else{
            echo 'Something went wrong';
        }
    }

    public function roomUsers()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $room_id = $data['room_id'];

        $RoomHistory = $this->model('RoomHistory');
        $users = $RoomHistory->getRoomUsers($room_id);
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
        $room_id = $data['room_id'];

        $RoomHistory = $this->model('RoomHistory');
        $users = $RoomHistory->getRoomCurrentUsers($room_id);
        if(count($users) === 0){
            echo "The room may have been expired or never existed";
        }else {
            echo json_encode($users);
        }
    }
}