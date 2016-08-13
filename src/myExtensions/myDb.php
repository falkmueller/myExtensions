<?php

namespace myExtensions;

use PDO;
use PDOException;


class myDb {
    
    private $_pdo;
    public $error = null;


    public function __construct($host, $database, $user, $pass)
    {
        
        $dsn = 'mysql:host=' . $host . ';dbname=' . $database;
        
        $options = array(
            PDO::ATTR_PERSISTENT    => true,
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
        );
         
        try {
            $this->_pdo = new PDO($dsn, $user, $pass, $options);
        }
        catch(PDOException $e) {
            $this->error = $e->getMessage();
        }
    }
    
    public function createQuery($query = "", $params = array()){
        $dp_query = new myDbStatement($this->_pdo);
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
        return $this->_pdo->quote($value);
    }
    
}