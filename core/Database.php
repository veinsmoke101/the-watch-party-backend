<?php

namespace app\core;

use PDO;
use PDOException;
use PDOStatement;

/**
 *
 * Database is a class that take care of the connection to the database
 * @author Taha Lechgar
 * @package app\core
 */
class Database
{
    protected PDO $pdo;
    protected PDOStatement $statment;


    public function __construct()
    {
        try{
            $this->pdo = new PDO($_ENV['DB_DSN'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }

    }

    /**
     * @param string $query
     * @return void
     */
    public function prepare(string $query)
    {
        $this->statment = $this->pdo->prepare($query);
    }

    /**
     * @param array|string $params
     * @return void
     */
    public function bind(array|string $params)
    {
        try{
            foreach ($params as $param => $value){
                    $this->statment->bindValue(":$param", $value);
            }

        }catch(PDOException $exception){
            echo 'Bind failed' . $exception->getMessage();
        }
    }


    /**
     * @return bool
     */
    public function execute(): bool
    {
        try{
           return $this->statment->execute();
        }catch(PDOException $exception){
            echo $exception->getMessage();
            return false;
        }
    }


    /**
     * @return mixed
     */
    public function getOneRecord()
    {
        $this->execute();
        return $this->statment->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * @return array|false
     */
    public function getMultipleRecords()
    {
        $this->execute();
        return $this->statment->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countRows(): int
    {
        $this->execute();
        return $this->statment->rowCount();
    }

    public function lastInsertedId(): bool|string
    {
        return $this->pdo->lastInsertId();
    }
}