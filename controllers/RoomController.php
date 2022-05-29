<?php



namespace app\controllers;


use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\models\Room;
use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Pusher\ApiErrorException;
use Pusher\Pusher;
use Pusher\PusherException;

class RoomController extends Controller
{
    private Pusher $pusher;

    public function __construct()
    {
        parent::__construct();
        $this->pusher = Application::$app->pusher;
    }

    public function newRoom()
    {
        $roomRef = uniqid('room_');

        $start_at = ( isset($_POST['start_at']) && !empty($_POST['start_at']) ) ? $_POST['start_at'] : date('Y-m-d H:i:s');
        $expire_at = ( isset($_POST['expire_at']) && !empty($_POST['expire_at']) ) ? $_POST['expire_at'] : null;

        $newRoom = array(
            "title"             => $_POST['title'],
            "created_at"        => date('Y-m-d H:i:s'),
            "start_at"          => $start_at,
            "expire_at"         => $expire_at,
            "author"            => $_POST['author'],
            "unique_reference"  => $roomRef
        );

        $Room = $this->model('Room');
        if($Room->insert($newRoom)){
            // trigger a pusher channel events with the room reference
            $this->pusher->trigger(
                $roomRef,
                'videoUrl',
                ''
            );

            $newRoomData = $Room->getRoomByRef($roomRef);

            $response = array(
                'status' => 'success',
                'data' => $newRoomData,
                'message' => "Room created successfully"
            );
            $response = json_encode($response);
            echo $response;

        }else{
            echo 'Something went wrong';
        }
    }

    /**
     *
     * @throws Exception
     */
    public function joinRoom()
    {
        // get Room id from the url
        $params = Application::$app->request->getRouteParams();
        $roomRef = $params["room_id"];
        $userId = $params["user_id"];

        // call models
        $Room = $this->model('Room');
        $RoomHistory = $this->model('RoomHistory');

        // handle the <<joinRoom>> process
        $roomData = $Room->getRoomByRef($roomRef);
        $today   = new DateTime(date('Y-m-d H:i:s'));
        $expire_at  = new DateTime($roomData["expire_at"]);

        //check if room is expired
        if($roomData["expire_at"] && $today >= $expire_at) {
            $this->response->setStatusCode(410);
            $response = array(
                'status' => 'error',
                'message' => "room expired"
            );
            echo json_encode($response);
            return;
        }

        $this->prepareUser($userId);

        $roomHistoryData = array(
            'room_id' => $roomData['id'],
            'user_id' => $userId,
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
            'message' => "joined $roomRef successfully"
        );
        $response = json_encode($response);
        echo $response;
    }


    public function leaveRoom()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $roomId = $data['room_id'];
        $userId = $data['user_id'];

        $conditions = array(
            'room_id' => $roomId,
            'user_id' => $userId
        );

        $currentDateTime = date('Y-m-d H:i:s');

        $RoomHistory = $this->model('RoomHistory');
        if($RoomHistory->setUserLeftAt($currentDateTime, $conditions)){
            echo 'user left the room successfully';
        }
    }

    public function newVideo()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $roomRef    = $data['roomRef'];
        $videoUrl   = $data['videoUrl'];


        if($this->checkIfRoomExpired($roomRef)) {
            $this->response->setStatusCode(410);
            $response = array(
                'status' => 'error',
                'message' => "room expired"
            );
            echo json_encode($response);
            return;
        }


        $this->pusher->trigger(
            $roomRef,
            'videoUrl',
            $videoUrl
        );
        echo 'video url sent successfully';
    }

    public function checkIfRoomExpired($roomRef): bool
    {
        $Room = $this->model('Room');

        $roomData = $Room->getRoomByRef($roomRef);
        $today   = new DateTime(date('Y-m-d H:i:s'));
        $expire_at  = new DateTime($roomData["expire_at"]);
        if($roomData["expire_at"] && $today >= $expire_at) {
            return true;
        }
        return false;
    }

    public function prepareUser($user_id)
    {
        $Room = $this->model('Room');
        $userJoined = $Room->checkIfUserInRoom($user_id);
        if(!$userJoined) return;

        $RoomHistory = $this->model('RoomHistory');
        $today = date('Y-m-d H:i:s');
        $RoomHistory->setUserLeftAt($today, array('user_id' => $user_id));
    }

}