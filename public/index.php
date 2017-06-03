<?php

define('DOCROOT_DIRECTORY_PATH', realpath(dirname(__FILE__)).'/'); //needed for AJAX apps

if (!defined('APPLICATION_PATH')){
	define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
}

if (!defined('APPLICATION_ENV')){
	if (getenv('APPLICATION_ENV')){
		$environmentName=getenv('APPLICATION_ENV');
	}
	else{
		$environmentName='production';
	}
    define('APPLICATION_ENV', $environmentName);
}

// Ensure library/ is on include_path
set_include_path(
	implode(
		PATH_SEPARATOR,
		array(
			realpath(APPLICATION_PATH . '/../library'),
			get_include_path(),
		)
	)
);

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/../../../configs/zendConfigs/application.ini'
);
$application->bootstrap()
            ->run();


echo "\n<!-- ".$environmentName." -->";