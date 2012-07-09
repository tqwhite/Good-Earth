<?php

class ModelTestCase extends PHPUnit_Framework_TestCase
{

	/**
	* @var \Bisna\Application\Container\DoctrineContainer
	**/
	protected $doctrineContainer;
	/**
	 * @var Zend_Application
	 **/
	protected $application;

    public function setUp()
    {
        global $application;
        $this->application=$application;
        $application->bootstrap();
        $this->doctrineContainer=Zend_Registry::get('doctrine');

		$em=$this->doctrineContainer->getEntityManager();

        $tool=new \Doctrine\ORM\Tools\SchemaTool($em);

		$metas=$em->getMetadataFactory()->getAllMetadata();


        $tool->createSchema($metas);

        parent::setUp();
    }

	public function tearDown(){

			$this->doctrineContainer->getConnection()->close();
			$em=$this->doctrineContainer->getEntityManager();
			$tool=new \Doctrine\ORM\Tools\SchemaTool($em);
			$tool->dropDatabase();

        parent::tearDown();

	}



}

