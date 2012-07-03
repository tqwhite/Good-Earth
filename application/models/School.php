<?php

class Application_Model_School
{


	public function __construct(){

	$this->doctrineContainer=\Zend_Registry::get('doctrine');
	$this->_entityManager=$this->doctrineContainer->getEntityManager();
	}


	public function getSchoolList(){

		$query = $this->_entityManager->createQuery('SELECT u from GE\Entity\School u');

		$schoolList = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
		return $schoolList;

	}

	public function getSchool($refId){

		$query = $this->_entityManager->createQuery('SELECT u from GE\Entity\School u WHERE u.refId = :refId');
		$query->setParameters(array(
			'refId' => $refId
		));
		$schoolList = $query->getResult();
		return $schoolList;

	}

}

