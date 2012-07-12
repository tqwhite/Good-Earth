<?php

class Application_Model_School
{


	public function __construct(){

	$this->doctrineContainer=\Zend_Registry::get('doctrine');
	$this->_entityManager=$this->doctrineContainer->getEntityManager();
	}


	public function getList($hydrationMode){

		$query = $this->_entityManager->createQuery('SELECT u from GE\Entity\School u');

		switch ($hydrationMode){
			default:
			case 'array':
				$list = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
				break;
			case 'record':
				$list = $query->getResult();
				break;
		}

		return $list;

	}

	public function getByRefId($refId){

		$query = $this->_entityManager->createQuery('SELECT u from GE\Entity\School u WHERE u.refId = :refId');
		$query->setParameters(array(
			'refId' => $refId
		));
		$schoolList = $query->getResult();
		return $schoolList;

	}

	static function formatOutput($identity){
		if (count($identity)<2){
			return self::formatScalar($identity);
		}
		else{

			$list=$identity;
			$outList=array();
			for ($i=0, $len=count($list); $i<$len; $i++){
				$outList[]=self::formatScalar($list[$i]);
			}
			return $outList;
		}

	}

	static function formatScalar($inData){
	return array(
					'name'=>$inData->name,
					'refId'=>$inData->refId,
					'gradeLevels'=>\Application_Model_GradeLevel::formatOutput($inData->gradeLevelNodes)
				);
	}

}

