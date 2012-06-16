<?php

error_reporting(E_ALL | E_STRICT);

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

require_once('ControllerTestCase.php');
require_once 'ModelTestCase.php';

require_once('PHP/Token/Stream/Autoload.php'); //fixed bug: Fatal error: Class 'PHP_Token_Stream' not found (per https://github.com/sebastianbergmann/phpunit/issues/353)

$application=
	new Zend_Application(APPLICATION_ENV,
		APPLICATION_PATH.'/configs/application.ini');
