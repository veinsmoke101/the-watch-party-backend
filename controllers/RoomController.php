<?php



namespace app\controllers;


use app\core\Application;
use app\core\Controller;
use DateTime;
use Exception;
use Predis\Client;
use Pusher\Pusher;

class RoomController extends Controller
{
    private Pusher $pusher;
    private Client $redisClient;


//    public function redisCheck()
//    {
//        $json = file_get_contents('php://input');
//        $data = json_decode($json, true);
//        $roomRef = $data['roomRef'];
//        echo json_encode($this->redisClient->lrange($roomRef, 0, -1));
//    }


    public function __construct()
    {
        parent::__construct();
        $this->pusher = Application::$app->pusher;
        $this->redisClient = new Client();
//        $this->messageController = new MessageController();
    }

    public function newRoom()
    {

        $roomRef = uniqid('room_');
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $start_at = ( isset($data['start_at']) && !empty($data['start_at']) ) ? $data['start_at'] : date('Y-m-d H:i:s');
        $expire_at = ( isset($data['expire_at']) && !empty($data['expire_at']) ) ? $data['expire_at'] : null;

        $payload = $this->checkUserAuthorization($data['author']);

        if(!$payload) return;


        $newRoom = array(
            "title"             => $data['title'],
            "created_at"        => date('Y-m-d H:i:s'),
            "start_at"          => $start_at,
            "expire_at"         => $expire_at,
            "author"            => $data['author'],
            "unique_reference"  => $roomRef
        );

        $Room       = $this->model('Room');
        
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

        $roomMessages = $this->redisClient->lrange($roomRef, 0, -1);

        $RoomHistory = $this->model('RoomHistory');
        $roomUsers = $RoomHistory->getRoomCurrentUsers($roomData['id']);

        $this->pusher->trigger(
            $roomRef,
            'roomUsersCount',
            count($roomUsers)
        );

        $User = $this->model('User');
        $NewUser = $User->getUserById($userId);

        // remove sensitive user data
        unset($NewUser['password']);
        unset($NewUser['email']);

        $this->pusher->trigger(
            $roomRef,
            'newUser',
            json_encode($NewUser)
        );

        $response = array(
            'status' => 'success',
            'roomData' => $roomData,
            'message' => "joined $roomRef successfully",
            'roomMessages' => $roomMessages,
            'roomUsers' => $roomUsers,
            'videoUrl' => $this->redisClient->get('videoUrl') ?? ''

        );
        $response = json_encode($response);
        echo $response;
    }


    public function leaveRoom()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $roomRef = $data['room_ref'];
        $userId = $data['user_id'];

        $Room = $this->model('Room');
        $room = $Room->getRoomByRef($roomRef);

        $conditions = array(
            'room_id' => $room['id'],
            'user_id' => $userId
        );

        $currentDateTime = date('Y-m-d H:i:s');

        $RoomHistory = $this->model('RoomHistory');

        if($RoomHistory->setUserLeftAt($currentDateTime, $conditions)){
            echo 'user left the room successfully';
        }

        $roomUsers = $RoomHistory->getRoomCurrentUsers($room['id']);

        $this->pusher->trigger(
            $roomRef,
            'roomUsersCount',
            count($roomUsers)
        );
        $this->pusher->trigger(
            $roomRef,
            'userLeft',
            $userId
        );
    }

    public function kickUser()
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $payload = $this->checkUserAuthorization($data['userId']);
        if(!$payload) return;

        $this->pusher->trigger(
            $data['roomRef'],
            'kickUser',
            $data['userToKick']
        );
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
        $this->redisClient->set('videoUrl', $videoUrl);
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