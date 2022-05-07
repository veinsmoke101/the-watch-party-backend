<?php

namespace app\core;


class Model
{

    protected Database $db;
    protected string $table;


    public function __construct( )
    {
        $this->db = new Database();
    }

    public function getAll()
    {
        $query = "SELECT * FROM $this->table";
        $this->db->prepare($query);
        return $this->db->getMultipleRecords();
    }

    public function getRecordByColumn($column, $value)
    {
        $query = "SELECT * FROM $this->table WHERE $column = :$column";
        $this->db->prepare($query);
        $this->db->bind($column, $value);
        $this->db->getMultipleRecords();
    }

    public function getRecordById($id)
    {
        $query = "SELECT * FROM $this->table WHERE id = :id";
        $this->db->prepare($query);
        $this->db->bind("id", $id);
        $this->db->getOneRecord();
    }

    public function insert($data): bool
    {
        $fields = array_keys($data);
        $values = array_keys($data);
        $query = "INSERT INTO $this->table (" . implode(',', $fields) . ')
        VALUES (:' . implode(', :', $fields) . ')';
        $this->db->prepare($query);
        $this->db->bind($data);
        return $this->db->execute();
    }
}