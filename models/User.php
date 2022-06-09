<?php

namespace app\models;

use app\core\Model;

class User extends Model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "users";
    }

    public function register($userData): bool
    {
        return $this->insert($userData);
    }

    public function checkUserByEmail($email){
        return $this->getOneRecordByColumn('email',$email);
    }

    public function getUserById($id)
    {
        return $this->getRecordById($id);
    }

}