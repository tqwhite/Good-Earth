<?php

class TestController extends Zend_Controller_Action
{

    public function init()
    {
		/* Initialize action controller here */
    }

    public function indexAction()
    {
		// action body
    }

    public function databaseAction()
    {

    echo "DATABASE\n";
		$locale=$this->getRequest()->getParam('locale');

	switch ($locale){
		case 'qDev':
			$db = new Zend_Db_Adapter_Pdo_Mysql(array(
				'host'     => '127.0.0.1',
				'username' => 'tq',
				'password' => '',
				'dbname'   => 'test1'
			));
		break;
		case 'demo':
			$db = new Zend_Db_Adapter_Pdo_Mysql(array(
				'host'     => 'localhost',
				'username' => 'goodearthsite',
				'password' => 'glory*snacks',
				'dbname'   => 'goodEarthDemoData'
			));
		break;

		}

		$stmt = $db->query('select * from example');

		print_r($stmt->fetch());
		echo '<p/>'.Zend_Version::VERSION;
    }

    public function doctrineAction()
    {

		$this->doctrineContainer=Zend_Registry::get('doctrine');
		$initSchema=$this->getRequest()->getParam('initSchema');

		$u=new GE\Entity\User();
		$u->firstName='tq';
		$u->lastName='white';
		$u->userName='tq'.  uniqid();
		$u->password='12345';
		$u->userName='tqwhite';

		$em=$this->doctrineContainer->getEntityManager();
		$em->persist($u);
		$em->flush();
		$em->clear();

		$users=$em
			->createQuery('select u from GE\Entity\User u')
			->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

/*
		foreach ($users as $user){
		    echo 'refId='.$user->refId.'<br/>';
		}
*/
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(false);



		$this->_helper->json(array(
			status=>1,
			data=>$users
		));



    }

    public function sqliteAction()
    {
		$db = new SQLite3('mysqlitedb.db');
		print_r($db);
    }

    public function initAction()
    {
		$this->view->message = "<b>Initializing Things</b><p/>";

		$initSchema=$this->getRequest()->getParam('initSchema');

		$this->doctrineContainer=Zend_Registry::get('doctrine');
			$em=$this->doctrineContainer->getEntityManager();

		if ($initSchema=='pleaseKillMyData'){
			$tool=new \Doctrine\ORM\Tools\SchemaTool($em);

			$tool->dropDatabase();
			$this->view->message .= "dropped database - bye bye Data!<br/>";

			$metas=$em->getMetadataFactory()->getAllMetadata();
			$tool->createSchema($metas);
			$this->view->message .= "initializing database<br/>";


			$this->view->sqlList=$tool->getCreateSchemaSql($metas);
        }
    }


}









