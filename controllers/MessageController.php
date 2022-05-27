<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use Pusher\Pusher;

class MessageController extends Controller
{

    private Pusher $pusher;

    public function __construct()
    {
        parent::__construct();
        $this->pusher = Application::$app->pusher;
    }

    public function newMessage()
    {
       $this->videoEvents('message');
    }

    public function pauseVideoMessage()
    {
        $this->videoEvents('pause');
    }

    public function playVideoMessage()
    {
        $this->videoEvents('play');
    }

    public function jumpVideoMessage()
    {
        $this->videoEvents('jump');
    }

    public function videoEvents($event)
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $roomRef    = $data['roomRef'];
        $message   = ['message' => $data['message']];
        if(array_key_exists('time',$data))  $message['time'] = $data['time'];


        $this->pusher->trigger(
            $roomRef,
            $event,
            json_encode($message)
        );
        echo json_encode(["success" => "$event sent successfully"]);
    }
}