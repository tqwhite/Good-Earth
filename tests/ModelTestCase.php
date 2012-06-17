<?php

class ModelTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{

	/**
	* @var \Bisna\Application\Container\DoctrineContainer
	**/
	protected $doctrineContainer;

    public function setUp()
    {
        global $application;
        $this->application=$application;
        $application->bootstrap();
        $this->doctrineContainer=Zend_Registry::get('doctrine');

        $tool=new \Doctrine\ORM\Tools\SchemaTool($this->doctrineContainer->getEntityManager());

		$metas=$this->getClassMetas(APPLICATION_PATH.'/../library/GE/Entity/', 'GE\Entity\\');
        $tool->createSchema($metas);

        parent::setUp();
    }

	public function tearDown(){

        self::dropSchema($this->doctrineContainer->getConnection()->getParams());
        parent::tearDown();

	}


	public  function getClassMetas($path, $namespace){ //from video approx minute 35
		$metas=array();
		if ($handle =opendir($path)){
			while (false!== ($file=readdir($handle))){
				if (strstr($file, '.php')){
					list($class)=explode('.', $file);
					$metas[]=$this->doctrineContainer->getEntityManager()->getClassMetadata($namespace.$class);
				}
			}
		}
		return $metas;
	}

    public static function dropSchema($params)
    {
        if (file_exists($params['path']))
            unlink ($params['path']);
    }


}

