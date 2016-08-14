<?php

namespace myExtensions;

class myEntity {
    public static $db;
    
    protected static $_table = null;
    protected $_data = array();
    
    public static function fields(){
        return array();
    }

    public function __construct($data = array()){
        $fields = static::fields();
        foreach ($fields as $key => $field){
            $this->$key = (isset($data[$key]) ? $data[$key] : (isset($field["default"]) ? $field["default"] : null));
            unset ($data[$key]);
        }

        foreach ($data as $key => $value){
            $this->$key = $value;
        }

    }

    public function __get($name) {
        if(isset($this->_data[$name])){
            return $this->_data[$name];
        }
        
        return null;
    }
    
    public function __set($name, $value){
        $this->_data[$name] = $value;
    }
    
    public function toArray(){
        $array = get_object_vars($this);
        $array = array_merge($this->_data, $array);
        unset($array["_data"]);
        
        return $array;
    }
    
    /*SQL FUnctions #######################################*/
    public function insert(){
        $col_names = array();
        $col_values = array();
        $auto_id_field = null;
        
        $fields = static::fields();
        foreach ($fields as $key => $field){
            $col_names[] = "`".$key."`";
            $col_values[":".$key] = $this->$key;
            
            if(!empty($field["autoincrement"])){
                $auto_id_field = $key;
            }
        }
        
        $sql = "INSERT INTO `".static::$_table."` (".implode(",", $col_names).") VALUES (".implode(",", array_keys($col_values)).")";
        
        
        $statement = SELF::$db->createQuery($sql, $col_values);
        if($statement->execute()){
            if($auto_id_field){
               $this->$auto_id_field = $statement->lastInsertId();
            }
            
            return true;
        }
        
        return false;
        
    }
    
    public function update($Fields = array()){
        $updates = array();
        $where = array();
        $col_values = array();
        
        $fields = static::fields();
        foreach ($fields as $key => $field){
            if (!empty($field["primary"])){
                $where[] = "`{$key}` = :{$key}";
                $col_values[":".$key] = $this->$key;
                continue;
            }
            
            if(!empty($Fields) && !in_array($key, $Fields) ){
                continue;
            }
            
            $updates[] = "`{$key}` = :{$key}";
            $col_values[":".$key] = $this->$key;
        }
        
        $sql = "UPDATE `".static::$_table."` SET ".implode(" , ", $updates)." WHERE ".implode(" AND ", $where);
        
        $statement = SELF::$db->createQuery($sql, $col_values);
        if($statement->execute()){
            return true;
        }
        
        return false;
    }
    
    public function exists(){
        $where = array();
        
        $fields = static::fields();
        foreach ($fields as $key => $field){
            if (!empty($field["primary"])){
                $where[$key] = $this->$key;
            }
        }
        
        if(static::selectOne($where)){
            return true;
        }
        
        return false;
    }

    public static function selectOne($where = array()){
        $res = static::select($where, "", 0, 1);
        
        if(empty($res)){
            return null;
        }
        
        return $res[0];
    }

    public static function select($where = array(), $order = "", $offset = 0, $limit = 0){
        $sql = "SELECT * FROM `".static::$_table."`";
        $params = array();
        
        if(!empty($where)){
            $where_sql = array();
            $fields = array_keys(static::fields());
            foreach ($where as $field => $value){
                if(!in_array($field, $fields)){
                    continue;
                }
                
                $where_sql[] = "`{$field}` = :{$field}";
                $params[":".$field] = $value;
            }
            
            if(!empty($where_sql)){
                $sql .= " WHERE ".implode(" AND ", $where_sql);
            }
        }
        
        if($order){
            $sql .= " ORDER BY {$order}";
        }
        
        if($offset || $limit){
            $sql .= " LIMIT ".intval($offset).", ".$limit;
        }
        
        $statement = SELF::$db->createQuery($sql, $params);
        $statement->execute();
        
        $result = array();
        while($array = $statement->fetch(false)){
            $result[] = new static($array);
        }
        
        return $result;
    }
    
    
}

