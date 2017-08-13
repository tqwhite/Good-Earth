<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

public function _initConfigAndRegistry(){
	
	$config=$this->getOptions();
	
	if (isset($config['emailSender'])){
	Zend_Registry::set('emailSender', $config['emailSender']);
	}
	else{
	
	Zend_Registry::set('emailSender', '');
	}

}

public function _initSession(){

error_reporting(E_ERROR | E_PARSE & ~E_WARNING & ~E_NOTICE); //error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

	$config=$this->getOptions();
	Zend_Registry::set('store', $config['store']);
	
	$config=$this->getOptions();
	Zend_Registry::set('databaseSpecs', $config['resources']['doctrine']['dbal']['connections']['default']['parameters']);
	
	Zend_Registry::set('authorize', $config['authorize']);
	Zend_Registry::set('payment', $config['payment']);
	
	Zend_Registry::set('showHelixDebug', $_GET['showHelixDebug']);

	Zend_Session::start();

	$front = Zend_Controller_Front::getInstance();
//	$front->registerPlugin(new Q\Plugin\Authorize\Check());


	$config=$this->getOptions();
	Zend_Registry::set('helixConfiguration', $config['helix']['configuration']);
}
public function _initRouting(){

	$front = Zend_Controller_Front::getInstance();
	$router = $front->getRouter();

	$route = new Zend_Controller_Router_Route(
		'test',
		array(
			'controller' => 'bookmarks',
			'action'     => 'index'
		)
	);
	$router->addRoute('test', $route);

	$route = new Zend_Controller_Router_Route(
		'confirm/:confirmcode',
		array(
			'controller' => 'user',
			'action'     => 'confirm'
		)
	);
	$router->addRoute('confirm', $route);
}

}

