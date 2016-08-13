<?php

namespace myExtensions;

use PDO;
use PDOException;


class myDbStatement {
    
    private $_myDb;
    private $_stmt;
    
    private $_query = "";
    private $_params = array();
    private $_params_multible = array();

    public $error = null;

    public function __construct(&$myDb)
    {
        $this->_myDb = $myDb;
    }
    
    public function setQuery($query){
        $this->_query = $query;
    }
    
    public function execute()
    {
        $params = $this->_params;
        $query = $this->_query;
        
        if($this->_params_multible){
            foreach ($this->_params_multible as $name => $multible_params){
                $param_keys = "";
                
                $index = 0;
                foreach($multible_params["value"] as $value){
                    $index++;
                    $sinle_key = $name.'_multi_'.$index;
                    $params[$sinle_key] = array("value" => $value, "type" => $multible_params["type"]);
                    if($param_keys != ""){$param_keys .= ",";}
                    $param_keys .= $sinle_key;
                }
                
                $query = str_replace($name, $param_keys, $query);
            }
        }
        
        $this->_stmt = $this->_myDb->pdo->prepare($query);

        if($params){
            foreach ($params as $name => $param){
                $this->_bind($name, $param["value"], $param["type"]);
            }
        }
        
        try {
            return $this->_stmt->execute();
        }
        catch(PDOException $e) {
            $this->error = $e->getMessage();
            $this->_myDb->setError($e);
            return false;
        }
        
    }
    
     public function fetchAll($exec = true)
    {
        if($exec){
            if(!$this->execute()){
            return null;
            }
        }
        return $this->_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function fetch($exec = true)
    {
        if($exec){
            if(!$this->execute()){
            return null;
            }
        }
        return $this->_stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function rowCount()
    {
        return $this->_stmt->rowCount();
    }
    
    public function lastInsertId()
    {
        return $this->_myDb->pdo->lastInsertId();
    }
    
    public function bind($param, $value, $type = null){
        
        if(is_array($value)){
            $this->_params_multible[$param] = array("value" => $value, "type" => $type);
        } else {
            $this->_params[$param] = array("value" => $value, "type" => $type);
        } 
    }

    private function _bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->_stmt->bindValue($param, $value, $type);
    }
    
}