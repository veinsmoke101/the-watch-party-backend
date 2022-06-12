<?php

namespace app\models;

use app\core\Model;

class RoomHost extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'room_host';
    }
}