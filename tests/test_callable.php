<?php

/*
 * 
  */
class test1 {
    
    public function test_call(){
        
    }
    
    public static function static_call(){
        
    }
    
}

if(is_callable(function(){})){
    echo "function(){} is callable<br/>";
}

if(is_callable("test1::static_call")){
    echo "test1::static_call is callable<br/>";
}

if(is_callable(array("test1","test_call"))){
    echo 'array("test1","test_call") is callable<br/>';
}

$t = new test1();

if(is_callable(array($t,"test_call"))){
    echo 'array($t,"test_call") is callable<br/>';
}