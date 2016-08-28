<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once '../src/myHook.php';

use myExtensions\myHook;

$hooks = myHook::Instance();

$hooks->addFilter("testfilter", function($value){ return "filter-".$value;});
echo $hooks->filter("testfilter", 1);
echo "<br/>";


$hooks->addMiddleware("m", function($res, $next){ 
    $res .= " M1-before "; 
    $res = $next($res);
    $res .= " M1-after ";
    
    return $res;
    
});

class x {
    
    static function y ($res, callable $next,$z1, $z2){
        $res .= " M2-bevore ";
        $res = $next($res);   
        $res .= " M2-after ";

        return $res;
    }
    
    public function call_me($z1, $z2, $res = ''){
        $res .= " core ";
        return $res;
    }
    
}

$hooks->addMiddleware("m", "x::y");


$x = new x();
$res = $hooks->middleware("m", array($x, "call_me"),"", 1, 2);

echo $res;
