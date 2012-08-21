<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

public function _initSession(){


	$config=$this->getOptions();
	Zend_Registry::set('store', $config['store']);

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

