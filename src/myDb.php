<?php

namespace myExtensions;

use PDO;
use PDOException;


class myDb {
    
    public $pdo;
    public $error = null;
    public $errorhandler = null;


    public function __construct($host, $database, $user, $pass, $errorHandler = null)
    {
        
        if($errorHandler){
            $this->errorhandler = $errorHandler;
        }
        
        $dsn = 'mysql:host=' . $host . ';dbname=' . $database;
        
        $options = array(
            PDO::ATTR_PERSISTENT    => true,
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
        );
         
        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        }
        catch(PDOException $e) {
            $this->setError($e);
        }
    }
    
    public function createQuery($query = "", $params = array()){
        $dp_query = new myDbStatement($this);
        if($query){
            $dp_query->setQuery($query);
        }
        
        if($params){
            foreach ($params as $name => $param){
                $dp_query->bind($name, $param);
            }
        }
        
        return $dp_query;
    }

    public function quote($value){
        return $this->pdo->quote($value);
    }
    
    public function setError(\Exception $exception){
        $this->error = $exception->getMessage();
        if ($this->errorhandler && is_callable($this->errorhandler)) {
            $f = $this->errorhandler;
            $f($exception);
        }
    }
    
}