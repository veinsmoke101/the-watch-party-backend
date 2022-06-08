<?php

namespace app\models;

use app\core\Model;

class RoomHistory extends Model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'room_history';
    }

    public function store($data): bool
    {
        return $this->insert($data);
    }

    public function getRoomUsers($room_id)
    {
        return $this->getRecordByColumn('room_id', $room_id);
    }

    public function getRoomCurrentUsers($room_id): bool|array
    {
        $query = "select users.username, users.image, users.id from room_history join users on room_history.user_id = users.id where room_id = :room_id and user_left_at is null";
        $this->db->prepare($query);
        $this->db->bind(array('room_id' => $room_id));
        return $this->db->getMultipleRecords();
    }

    public function getUserRooms($user_id)
    {
        return $this->getRecordByColumn('user_id', $user_id);
    }

    public function setUserLeftAt(string $userLeftAt, array $where): bool
    {
        return $this->updateColumnWithConditions('user_left_at', $userLeftAt, $where);
    }
}