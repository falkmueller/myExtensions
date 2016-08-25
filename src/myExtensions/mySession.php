<?php

namespace myExtensions;

class mySession {

    private static $_instance = null;
    
    public static $refresh_rate = 0;
    public static $use_fingerprint = true;
    
    public static function Instance(){
        
        if (self::$_instance == null) {
            $class = get_called_class();
            $ref = new \ReflectionClass($class);
            self::$_instance = $ref->newInstanceArgs(func_get_args());      
        }
        
        return self::$_instance;
    }
    
    public function __construct(){
        $this->start();
    }
   
    public function start()
    {
       if (session_id() === '') {
            if (@!session_start()) {
                return false;  
            }
            
            if(self::use_fingerprint && !$this->isFingerprint()){
                $this->destroy();
                return $this->start();
            }
            
            if(self::refresh_rate){
                return mt_rand(0, self::refresh_rate) === 0 ? $this->refresh() : true;
            }
        }
        return true;
    }

    public function refresh()
    {
        return session_regenerate_id(true);
    }

    public function isFingerprint()
    {
        $hash = md5(
            $_SERVER['HTTP_USER_AGENT'] .
            (ip2long($_SERVER['REMOTE_ADDR']) & ip2long('255.255.0.0'))
        );
        if (isset($_SESSION['_fingerprint'])) {
            return $_SESSION['_fingerprint'] === $hash;
        }
        $_SESSION['_fingerprint'] = $hash;
        return true;
    }
    
    public function destroy(){
        return session_destroy();
    }


    public function get($name, $default = null)
    {
        $parsed = explode('.', $name);
        $result = $_SESSION;
        while ($parsed) {
            $next = array_shift($parsed);
            if (isset($result[$next])) {
                $result = $result[$next];
            } else {
                return $default;
            }
        }
        return $result;
    }
    public function put($name, $value)
    {
        $parsed = explode('.', $name);
        $session =& $_SESSION;
        while (count($parsed) > 1) {
            $next = array_shift($parsed);
            if ( ! isset($session[$next]) || ! is_array($session[$next])) {
                $session[$next] = [];
            }
            $session =& $session[$next];
        }
        $session[array_shift($parsed)] = $value;
    }
}