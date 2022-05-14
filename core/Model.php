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
        $data = array($column => $value);
        $this->db->bind($data);
        return $this->db->getMultipleRecords();
    }

    public function getOneRecordByColumn($column, $value)
    {
        $query = "SELECT * FROM $this->table WHERE $column = :$column";
        $this->db->prepare($query);
        $data = array($column => $value);
        $this->db->bind($data);
        return $this->db->getOneRecord();
    }

    public function getRecordById($id)
    {
        $query = "SELECT * FROM $this->table WHERE id = :id";
        $this->db->prepare($query);
        $data = array("id" => $id);
        $this->db->bind($data);
        $this->db->getOneRecord();
    }

    public function insert($data): bool
    {
        $fields = array_keys($data);
        $query = "INSERT INTO $this->table (" . implode(',', $fields) . ')
        VALUES (:' . implode(', :', $fields) . ')';
        $this->db->prepare($query);
        $this->db->bind($data);
        return $this->db->execute();
    }

    public function updateColumnWithConditions($columnToUpdate, $value, $conditions): bool
    {
        $conditionColumns = array_keys($conditions);
//        $conditionValue = array_values($conditions);

        $whereClause = '';

        foreach ($conditionColumns as $condition){
            $whereClause .= "$condition = :$condition";
        }

        $data = array(
            $columnToUpdate => $value,
            ...$conditions
        );

        $query = "Update $this->table set $columnToUpdate = :$columnToUpdate where " . $whereClause;
        $this->db->prepare($query);
        $this->db->bind($data);
        return $this->db->execute();

    }
}