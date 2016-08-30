<?php 

namespace myExtensions;

class myTemplate {
    
    private static $_instance = null;
    
    private $_variables = array();
    private $_functions = array();
    private $_settings = array();

    public static function Instance(){
       
        if (self::$_instance == null) {
            $class = get_called_class();
            $ref = new \ReflectionClass($class);
            self::$_instance = $ref->newInstanceArgs(func_get_args());      
        }
        
        return self::$_instance;
    }
    
    public function setSetting($name, $value) 
    {
        $this->_settings[$name] = $value;
    }
    
    public function getSetting($name) 
    {
        if(isset($this->_settings[$name])){
            return $this->_settings[$name];
        }
        
        return null;
    }
    
    public function __set($name, $value) 
    {
        $this->_variables[$name] = $value;
    }
    
    public function __get($name) 
    {
        if(isset($this->_variables[$name])){
            return $this->_variables[$name];
        }
        
        return null;
    }
    
    public function addFunction($name, callable $function){
         $this->_functions[$name] = $function;
    }


    public function __call($name, $arguments) 
    {
        if(isset($this->_functions[$name])){
            return call_user_func_array($this->_functions[$name], $arguments);
        }
        
        return null;
    }

    public function render($template, $variables = array()){
        
        $view = new myView($this);
        return $view->render($template, $variables);
    } 
    
}

class myView {
   
    private $_engine = null;
    private $_blocks = array();
    private $_extent = null;
    private $_variables = array();


    public function __construct($engine){
        $this->_engine = $engine;
    }
    
    public function __set($name, $value) 
    {
        $this->_variables[$name] = $value;
    }
    
    public function __get($name) 
    {
        if(isset($this->_variables[$name])){
            return $this->_variables[$name];
        }
        
        return $this->_engine->$name;
    }
    
    public function __call($name, $arguments) 
    {
        return call_user_func_array(array($this->_engine, $name), $arguments);
    }
    
    public function render($template, $variables = array()){
        
        $this->_extent = null;
        $this->_variables = array_merge($this->_variables, $variables);
     
        ob_start();
        include($this->_engine->getSetting("dir").$template);
        $content = ob_get_contents();
        ob_end_clean();
        
        if($this->_extent){
            return $this->render($this->_extent, $variables);
        }
        
        return $content;
    } 
    
    public function startBlock($name, $mode = 'REPLACE'){
         ob_start();
        
        if(isset($this->_blocks[$name])){
            if($this->_blocks[$name]["mode"] == 'PREPEND'){
                echo $this->_blocks[$name]["content"];
            }
        } else {
            $this->_blocks[$name] = array(
                "mode" => $mode,
                "content" => NULL);
        }
        
        $this->_blocks[$name]["_next_mode"] = $mode;
    }
    
    public function endBlock($name){
        if(isset($this->_blocks[$name]) && $this->_blocks[$name]["content"] !== null){
            if($this->_blocks[$name]["mode"] == 'APPEND'){
                echo $this->_blocks[$name]["content"];
            }
        }
        
        $content = ob_get_contents();
        ob_end_clean();
        
       if(isset($this->_blocks[$name]) && $this->_blocks[$name]["content"] !== null){
           if($this->_blocks[$name]["mode"] == 'REPLACE'){
                $content = $this->_blocks[$name]["content"];
            }
       }
        
        $this->_blocks[$name]["content"] = $content;
        $this->_blocks[$name]["mode"] = $this->_blocks[$name]["_next_mode"];
        
        echo $content;
    }
    
    public function extend($template){
        $this->_extent = $template;
    }
    
    public function insert($template, $variables = array()){
        $view = new myView($this->_engine);
        echo $view->render($template, array_merge($this->_variables, $variables));
    }
}