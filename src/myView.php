<?php 

namespace myExtensions;

class myView {
    
    private static $_instance = null;
    
    public static function Instance(){
       
        if (self::$_instance == null) {
            $class = get_called_class();
            $ref = new \ReflectionClass($class);
            self::$_instance = $ref->newInstanceArgs(func_get_args());      
        }
        
        return self::$_instance;
    }
    
    
    
}

//
//namespace core\response;
//
//class view extends \core\obj implements response {
//    
//    public $template;
//    public $data = array();
//    private $deep = 1;
//    private $wrap = null;
//
//    public function init($template = "", $data = array(), $deep = 0){
//        
//        $this->template = $template;
//        $this->data = $data;
//        $this->deep = $deep + 1;
//    }
//
//    public function output(){
//        if($this->deep > 10){
//           echo "ERROR: Template {$this->template} is to deep. Loop?";
//           return;
//        }
//        
//        if(!$this->template){
//            $this->template = "error";
//            $this->data["message"] = "no template set";
//        }
//        
//        if(!file_exists(BASEDIR."/view/{$this->template}.phtml")){
//            $this->data["message"] = "template {$this->template} not exists";
//            $this->template = "error";
//        }
//        
//        //Variablen in Scope übernehmen
//        extract($this->data);
//        $router = \core\router::Instance();
//        
//        //template einbinden
//        ob_start();
//        include(BASEDIR."/view/{$this->template}.phtml");
//        $page = ob_get_contents();
//        ob_end_clean();
//        
//        //umschließendes template prüfen
//        if($this->wrap){
//            $this->wrap->data["child"] = $page;
//            $this->wrap->output();
//        } else {
//            echo $page;
//        }
//    }
//    
//    private function url($routeName, array $params = array()){
//        try {
//            return \core\router::Instance()->generate($routeName, $params);
//        } catch (\Exception $ex) {
//            return  \core\router::Instance()->basePath.$routeName;
//        }
//        
//    }
//
//    private function insert($template, $data = array()){
//        $view = self::Create($template,  array_merge($this->data, $data), $this->deep);
//        $view->output();
//    }
//    
//    private function wrap($template, $data = array()){
//        $this->wrap = self::Create($template,  array_merge($this->data, $data), $this->deep);
//    }
//    
//}
//

