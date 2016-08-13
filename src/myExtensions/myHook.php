<?php

namespace myExtensions;

class myHook {
    
    private static $_instance = null;
    
    public static function Instance(){
       
        if (self::$_instance == null) {
            $class = get_called_class();
            $ref = new \ReflectionClass($class);
            self::$_instance = $ref->newInstanceArgs(func_get_args());      
        }
        
        return self::$_instance;
    }
 
    private $hooks = array();

    public function registrate($hook_name, $function){
        if(!array_key_exists($hook_name, $this->hooks)){
             $this->hooks[$hook_name] = array();
        }

        $this->hooks[$hook_name][] = $function;
    }

    public function notify($hook_name, $instance, $data = null){

        if(!array_key_exists($hook_name, $this->hooks)){
            return;
        }

        foreach ( $this->hooks[$hook_name] as $hook_function){
            $hook_function($instance, $data);
        }
    }

    public function filter($hook_name, $instance, $returnvalue, $data = null){

        if(!array_key_exists($hook_name, $this->hooks)){
            return $returnvalue;
        }

        foreach ( $this->hooks[$hook_name] as $hook_function){
            $returnvalue = $hook_function($instance, $returnvalue, $data);
        }

        return $returnvalue;
    }

}
