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
}