<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Response;
use app\models\User;
use Firebase\JWT\JWT;

class AuthController extends Controller
{

    private User $user;



    public function __construct()
    {
        parent::__construct();
        $this->user = new User();
    }

    public function register()
    {
        define("DEFAULT_IMAGE", 'profile-image.svg');
        $data = json_decode(file_get_contents('php://input'), true);
        $userData = [
            'username' => $data['username'],
            'image' => DEFAULT_IMAGE,
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT)
        ];

        if(!$this->user->register($userData)){
            $response = [
                'status'    => 'error',
                'message'   => 'Oops Something went wrong during registration'
            ];
            echo json_encode($response);
        }

        unset($userData['password']);
        $userData['id'] = $this->user->getLastInsertedId();
        echo $this->generateJWT($userData);
    }

    public function login()
    {

        $data = json_decode(file_get_contents('php://input'), true);
        $userData = $this->user->checkUserByEmail($data['email']);

        if(!$userData) {
            $response = [
                'status' => 'error',
                'message' => 'email not found'
            ];
            echo json_encode($response);
            return;
        }

        if(!password_verify($data['password'], $userData['password'])){
            $response = [
                'status' => 'error',
                'message' => 'password is incorrect'
            ];
            echo json_encode($response);
            return;
        }
        unset($userData['password']);
        echo $this->generateJWT($userData);
    }

    public function generateJWT($data): bool|string
    {

        $payload = [
            'iss'   => 'localhost',
            'aud'   => 'localhost',
            'exp'   => time() + 10000,
            'data'  => $data
        ];
        $jwt = JWT::encode($payload, $_ENV['SECRET_KEY'], 'HS256');
        setcookie(name: "jwt", value: $jwt,httponly: true, expires_or_options: time()+60*60*24);

//        unset($data['id']);

        $response = [
            'status'    => 'success',
            'data'      => $data
        ];

        return json_encode($response);
    }

}