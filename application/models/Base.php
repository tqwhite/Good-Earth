<?php

class Application_Model_Base
{
	protected $doctrineContainer;
	protected $entityManager;
	protected $entityName;
	protected $entity;

	public function __construct(){
		$this->entityName=static::entityName;

		$this->doctrineContainer=\Zend_Registry::get('doctrine');
		$this->entityManager=$this->doctrineContainer->getEntityManager();
	}

	public function __get($property){
		return $this->$property;
	}

	public function __set($property, $value){
		$this->$property=$value;
	}

	public function generate(){
		$entityClassName="GE\\Entity\\{$this->entityName}";
		$this->entity=new $entityClassName();
		return $this->entity;
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
		$this->entity=$list[0];
		return $this->entity;

	}

	public function newFromArrayList($source, $suppressFlush){

		$outArray=array();

		if (!isset($suppressFlush)){ $suppressFlush=false; }

		foreach ($source as $objArray){

			$this->generate();

			foreach ($objArray as $label=>$data){
				$this->entity->$label=$data;
			}

			$this->entityManager->persist($this->entity);

			if (!$suppressFlush){
				$this->entityManager->flush();
			}

			$outArray[]=$this->entity;
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

	public function persistAndFlush(){
		$this->entityManager->persist($this->entity);
		$this->entityManager->flush();
	}

	static function formatOutput($inData, $outputType){
		if (count($inData)<2){
			return static::formatScalar($inData, $outputType);
		}
		else{

			$list=$inData;
			$outList=array();
			for ($i=0, $len=count($list); $i<$len; $i++){
				$outList[]=static::formatScalar($list[$i], $outputType);
			}
			return $outList;
		}

	}

	static function formatScalar($inData, $outputType){

		foreach ($inData as $label=>$data){
			$hasZeroProperty=true;
		}

		if ($hasZeroProperty){
			$inData=$inData[0];
		}
		$outArray=static::formatDetail($inData, $outputType);
		if ($hasZeroProperty){
			return array($outArray);
		}
		else{
			return $outArray;
		}

	}
}

