<?php

class Application_Model_Base
{
	protected $doctrineContainer;
	protected $entityManager;
	protected $entityName;

	public function __construct(){
		$this->entityName=static::entityName;

		$this->doctrineContainer=\Zend_Registry::get('doctrine');
		$this->entityManager=$this->doctrineContainer->getEntityManager();
	}

	public function generate(){
		$entityClassName="\\GE\\Entity\\{$this->entityName}";
		return new $entityClassName();
	}

	public function getList($hydrationMode){

		$query = $this->entityManager->createQuery("SELECT u from GE\\Entity\\{$this->entityName} u");

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

		$query = $this->entityManager->createQuery("SELECT u from GE\\Entity\\{$this->entityName} u WHERE u.refId = :refId");
		$query->setParameters(array(
			'refId' => $refId
		));
		$list = $query->getResult();
		return $list;

	}

	public function newFromArrayList($source, $suppressFlush){

		$outArray=array();

		if (!isset($suppressFlush)){ $suppressFlush=false; }

		foreach ($source as $objArray){

			$newObj=$this->generate();

			foreach ($objArray as $label=>$data){
				$newObj->$label=$data;
			}

			$this->entityManager->persist($newObj);

			if (!$suppressFlush){
				$this->entityManager->flush();
			}

			$outArray[]=$u;
		}
		return $outArray;
	}

	public function updateFromArray($inObj, $inArray, $suppressFlush){

		foreach ($inArray as $label=>$data){
			$inObj->$label=$data;
		}

		$this->entityManager->persist($inObj);

		if (!$suppressFlush){
			$this->entityManager->flush();
		}
	}

	static function formatOutput($inData){
		if (count($inData)<2){
			return static::formatScalar($inData);
		}
		else{

			$list=$inData;
			$outList=array();
			for ($i=0, $len=count($list); $i<$len; $i++){
				$outList[]=static::formatScalar($list[$i]);
			}
			return $outList;
		}

	}

}

