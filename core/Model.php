<?php

namespace app\core;

use Exception;

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
        return $this->db->getOneRecord();
    }

    public function getLastInsertedId(): bool|string
    {
        return $this->db->lastInsertedId();
    }

    public function insert($data): bool
    {
        $fields = array_keys($data);
        $query = "INSERT INTO $this->table (" . implode(',', $fields) . ')
        VALUES (:' . implode(', :', $fields) . ')';
        $this->db->prepare($query);
        $this->db->bind($data);
        try{
            return $this->db->execute();
        }catch(Exception $exception){
            echo $exception;
            die();
        }
    }

    public function updateColumnWithConditions($columnToUpdate, $value, $conditions = []): bool
    {
        $conditionColumns = array_keys($conditions);

        $whereClause = '';

        for ($i = 0; $i < count($conditionColumns); $i++){
            $whereClause .= "$conditionColumns[$i] = :$conditionColumns[$i]";
            if($i < count($conditionColumns) - 1){
                $whereClause .= ' and ';
            }
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