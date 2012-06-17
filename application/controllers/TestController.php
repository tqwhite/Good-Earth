<?php

class TestController extends Zend_Controller_Action {

	public function init() {
		/* Initialize action controller here */
	}

	public function indexAction() {
		// action body
	}

	public function databaseAction() {
		$db = new Zend_Db_Adapter_Pdo_Mysql(array(
			'host'     => '127.0.0.1',
			'username' => 'tq',
			'password' => '',
			'dbname'   => 'test1'
		));

		$stmt = $db->query('select * from example');

		print_r($stmt->fetch());
		echo Zend_Version::VERSION;
	}

	public function doctrineAction() {
		$message = $this->_getParam('m');

		if (!$message) {
			$message = 'default';
		}
		$this->view->message = $message;


		$this->doctrineContainer=Zend_Registry::get('doctrine');
		$u=new GE\Entity\User();
		$u->firstName='tq';
		$u->lastName='white';
		$u->userName='tq'.  uniqid();

		$em=$this->doctrineContainer->getEntityManager();
		$em->persist($u);
		$em->flush();
		$em->clear();


		$users=$em->createQuery('select u from GE\Entity\User u')->execute();
		foreach ($users as $user){
		    echo 'refId='.$user->refId.'<br/>';
		}

//		    $u=new GE\Entity\User();
//		   var_dump($u);
	}

	public function sqliteAction() {
		$db = new SQLite3('mysqlitedb.db');
		print_r($db);
	}


}







