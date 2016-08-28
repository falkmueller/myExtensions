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
 
    private $_middleware = array();
    
    private $_filter = array();

    public function addMiddleware($name, callable $callable, $position = 0){
        if(!array_key_exists($name, $this->_middleware)){
             $this->_middleware[$name] = array();
        }
        
        if (!$position){
            array_push($this->_middleware[$name], $callable);
            return;
        }
        
        array_splice($this->_middleware[$name], $position, 0,array($callable));
    }
    
     public function middleware($name, callable $callable, $returnvalue = null){
         
         $data = func_get_args();
         $data = array_splice($data,3);
         
        if(!array_key_exists($name, $this->_middleware)){
            return $callable($returnvalue, $data);
        }

        $next = function($returnvalue, $data = null) use ($name, $callable, &$next, $data){
            
            $f = next($this->_middleware[$name]);
            if(!$f){
                array_push($data, $returnvalue);
                return call_user_func_array($callable, $data);
            }
            
            array_unshift($data,$returnvalue, $next);

            return call_user_func_array($f, $data);
        }; 
        
        $first = reset($this->_middleware[$name]);
        array_unshift($data,$returnvalue, $next);
        return call_user_func_array($first, $data);
    }
    
    public function addFilter($name, callable $callable, $position = 0){
        if(!array_key_exists($name, $this->_filter)){
             $this->_filter[$name] = array();
        }
        
        if (!$position){
            array_push($this->_filter[$name], $callable);
            return;
        }
        
        array_splice($this->_filter[$name], $position, 0,array($callable));
    }
    
    public function filter($name, $returnvalue = null, $data = null){

        if(!array_key_exists($name, $this->_filter)){
            return $returnvalue;
        }

        foreach ( $this->_filter[$name] as $callable){
            $returnvalue = $callable($returnvalue, $data);
        }

        return $returnvalue;
    }
}