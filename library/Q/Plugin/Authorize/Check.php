<?php

namespace Q\Plugin\Authorize;

/**
*
* @author tqii
*
*
**/

class Check extends \Zend_Controller_Plugin_Abstract{

public function __construct(){
	//echo "hello from check() construtor\n";
}

public function routeStartup($request){
/*

	UNUSED PRESENTLY, MADE IT WORK BUT DIDN"T implementation
	SHOULD BE DONE IN models.XX.validate() FOR SECURITY

*/
	$auth = \Zend_Auth::getInstance();

	$adapter=new Adapter('tqwhite', 'passwordxxxd');
	$result = $auth->authenticate($adapter);

	if (!$result->isValid()){
	//	echo "notValid!\n";
		$request->setModuleName('default')
			->setControllerName('test')
			->setActionName('database')
			->setDispatched(false);
	}
}

public function credentials($credentials){
//\Q\Utils::dumpCli($credentials['userName'], 'Check::credentials($credentials[userName]');
	$auth = \Zend_Auth::getInstance();

	$adapter=new Adapter($credentials['userName'], $credentials['password']);
	$result = $auth->authenticate($adapter);

	return $auth;

}

} //end of class