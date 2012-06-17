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
   public function getTestUser($name = "John", $last='smith')
    {
        $user = new GE\Entity\User();
        $user->firstname = $name;
        $user->lastname = $last;
        return $user;
    }
    public function getApple()
    {
        $apple = new GE\Entity\Product();
        $apple->name = "Apples";
        $apple->amount = 2.45;
        $this->doctrineContainer->getEntityManager()->persist($apple);
        return $apple;
    }
    public function getOrange()
    {
        $orange = new GE\Entity\Product();
        $orange->name = "Oranges";
        $orange->amount = 2.99;
        $this->doctrineContainer->getEntityManager()->persist($orange);
        return $orange;
    }
    public function getPurchase()
    {
        $purchase = new GE\Entity\Purchase();
        $purchase->storeName = "My Store";
        return $purchase;
    }



}

