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
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $roomRef    = $data['roomRef'];
        $message   = $data['message'];

        var_dump($message);

        $this->pusher->trigger(
            $roomRef,
            'message',
            $message
        );
        echo 'message sent successfully';
    }
}