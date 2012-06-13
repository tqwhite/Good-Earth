<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

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
		'x',
		array(
			'controller' => 'bookmarks',
			'action'     => 'index'
		)
	);
	$router->addRoute('x', $route);
}

protected function X_initDoctrine()
{
//Q_Utils::dump($autoloader);

	require '/Doctrine/Common/ClassLoader.php';
    $classLoader = new \Doctrine\Common\ClassLoader('Doctrine');

    $classLoader->register();

    if (APPLICATION_ENV == 'development') {
        $cache = new \Doctrine\Common\Cache\ArrayCache;
    } else {
        $cache = new \Doctrine\Common\Cache\ApcCache;
    }


    $entitiesPath = '../library/Doctrine/domain/Entities';
    $proxiesPath    = '../library/Doctrine/domain/Proxies';

    $config = new \Doctrine\ORM\Configuration();
    $config->setMetadataCacheImpl($cache);
    $driverImpl = $config->newDefaultAnnotationDriver($entitiesPath);
    $config->setMetadataDriverImpl($driverImpl);
    $config->setQueryCacheImpl($cache);
    $config->setProxyDir($proxiesPath);
    $config->setProxyNamespace('domain\Proxies');

    if (APPLICATION_ENV == 'development') {
        $config->setAutoGenerateProxyClasses(true);
    } else {
        $config->setAutoGenerateProxyClasses(false);
    }

    $doctrineConfig = $this->getOption('doctrine');

    /*

    $connectionOptions = array(
        'driver'    => $doctrineConfig['conn']['driver'],
        'user'        => $doctrineConfig['conn']['user'],
        'pass'        => $doctrineConfig['conn']['pass'],
        'dbname'    => $doctrineConfig['conn']['dbname'],
        'host'        => $doctrineConfig['conn']['host']
    );
    */


    $connectionOptions = array(
        'driver'    => 'pdo_mysql',
        'user'        => 'root',
        'pass'        => 'dbPass',
        'dbname'    => 'test1',
        'host'        => 'localhost'
    );

/*
Current state is that it goes into EntityManager, line 792, and waits forever.
~/Documents/webdev/genericWhite/genericwhite.com/library/Doctrine/ORM/EntityManager.php

$config has the domain path strings in it. Perhaps their empty status is a problem.
*/

    $em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);
    Zend_Registry::set('em', $em);

    return $em;
}

}

