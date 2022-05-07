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
}