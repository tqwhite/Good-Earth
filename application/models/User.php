<?php

class Application_Model_User
{

	private $_doctrineContainer;
	private $_entityManager;

	public function __construct(){

	$this->doctrineContainer=\Zend_Registry::get('doctrine');
	$this->_entityManager=$this->doctrineContainer->getEntityManager();
	}

	public function getUserByUserId($userName){
		$query = $this->_entityManager->createQuery('SELECT u from GE\Entity\User u WHERE u.userName = :name');
		$query->setParameters(array(
			'name' => $userName
		));
		$users = $query->getResult();
		return $users;
	}
}

