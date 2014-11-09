<?php

class Q_Controller_Base extends Zend_Controller_Action {

    public function updateAuditInfo($employer='n/a')
    {
        $inData=$this->getRequest()->getPost('data');
        $debugObject=\Zend_Registry::get('debugObject');
        $debugObject['postData']=$this->cleanAuditInfo($inData);
        $debugObject['employer']=$employer;
        \Zend_Registry::set('debugObject', $debugObject);        
    }
    
    private function cleanAuditInfo($info){
    
    $name='password'; if (isset($info[$name])){$info[$name]='****';}
    $name='cardNumber'; if (isset($info[$name])){$info[$name]='****';}
    $name='cardData'; if (isset($info[$name])){$info[$name]='****';}

    return $info;
    }
    
    public function getFileName(){
        $obj = new ReflectionClass($this);
		$controllerName=$obj->name;
 		 return $controllerName;
   		
    }
    
	
protected function getAuditInfo(){
	$debugObject=\Zend_Registry::get('debugObject');
	return $debugObject;
}
}