<?php

class Q_Controller_Base extends Zend_Controller_Action {
    
    public function getFileName(){
        $obj = new ReflectionClass($this);
		$controllerName=$obj->name;
 		 return $controllerName;
   		
    }
}