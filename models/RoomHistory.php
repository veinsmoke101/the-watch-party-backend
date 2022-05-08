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
}