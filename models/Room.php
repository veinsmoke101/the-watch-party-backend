<?php

namespace app\models;

use app\core\Model;

class Room extends Model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'rooms';
    }

    public function store($data): bool
    {
        return $this->insert($data);
    }

    public function getRoomByRef($id)
    {
        return $this->getOneRecordByColumn("unique_reference", $id);
    }

    public function checkIfUserInRoom($user_id)
    {
        $query = "select * from room_history where user_id = :user_id and user_left_at is null";
        $this->db->prepare($query);
        $this->db->bind(array('user_id' => $user_id));
        return $this->db->getOneRecord();
    }

    public function setExpire($roomId, $date): bool
    {

        $conditions = [
            'id' => $roomId
        ];
        return $this->updateColumnWithConditions('expire_at', $date, $conditions);
    }
}