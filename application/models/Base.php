<?php

class Application_Model_Base
{
	const	yesFlush=true;
	const	noFlush=false;

	protected $doctrineContainer;
	protected $entityManager;
	protected $entityName;
	private $helixImportSpecs;
	protected $entity;
	
	private $specialFieldNameList;
	
	public static $dbConnection;

	public function __construct(){
		$this->entityName=static::entityName;
		

		if (defined('static::helixImportRelationName')){
			$this->helixImportSpecs=array(
				'viewName'=>static::helixImportViewName,
				'relationName'=>static::helixImportRelationName
			);
		}

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
		$this->entity->baseEntity=$this;
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

	public function getHelixSendList($hydrationMode){
		$query = $this->entityManager->createQuery("SELECT u from GE\\Entity\\{$this->entityName} u WHERE u.alreadyInHelix IS NULL or u.alreadyInHelix=0");

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

	public function persist($flushToo){
		try{
			$this->entityManager->persist($this->entity);
		}
		catch(Exception $e){
    		error_log('ERROR: this->entityManager->persist: '.$e->getMessage());
		}
		if ($flushToo){
			try{
				$this->entityManager->flush();
			}
			catch(Exception $e){
				error_log('ERROR: this->entityManager->flush: '.$e->getMessage());
			}
		}
	}

	public function flush(){
		$this->entityManager->persist($this->entity);
			$this->entityManager->flush();
	}

	static function formatOutput($inData, $outputType){

		$isList=Q\Utils::isList($inData);
		$oneItemList=($isList && count($inData)==1);
		if (get_class($inData)!='Doctrine\ORM\PersistentCollection'
			&& ($oneItemList || !$isList)){
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
		//this is called only if it's associative or has one numbered element
		$isList=Q\Utils::isList($inData);
		if ($isList){
			$inData=$inData[0]; //pop if off
		}

		$outArray=static::formatDetail($inData, $outputType);
		if ($isList){
			return array($outArray);
		}
		else{
			return $outArray;
		}

	}
	
	public function import($connectionObject){
	
		$helixData=$connectionObject->read($this->helixImportSpecs);
		$cleanArray=$this->mapHelixToEntity($entityObject, $helixData); //filters against entity properties
		return $cleanArray;
	}
	
	public function mapHelixToEntity($entityObject, $helixArray){
		$entityObject=$this->generate();

		$propertyList=$this->extractProperties($entityObject);
		$this->specialFieldNameList=array(
			'perYear full',
			'active?',
			'accountRefId',
			'dayRefId',
			'gradeLevelRefId',
			'mealRefId',
			'offeringRefId',
			'orderRefId',
			'purchaseRefId',
			'schoolRefId',
			'studentRefId',
			'userRefId',
			'helix id'
		); //these are weird helix names that don't match anything but that we want to let through for further processing
		$this->deleteFieldNameList=array(
			'perYear full',
			'active?',
			'helix id'
		); //these are weird helix names that don't match anything but that we want to let through for further processing

		foreach ($helixArray as $label=>$record){
			$outItemArray=array();
			foreach ($record as $fieldName=>$data2){
		
				if (isset($propertyList[$fieldName]) || in_array($fieldName, $this->specialFieldNameList)){
					$outItemArray[$fieldName]=$data2;
				}
			}			
			$outArray[]=$outItemArray;
		}
		
		return $outArray;
	} //end of method

	private function unsetSpecialFieldNames($inData){
		$outArrau=array();
		foreach ($inData as $label=>$data){
			if (!in_array($label, $this->deleteFieldNameList)){
				$outArray[$label]=$data;
			}
		}
		return $outArray;
	}
	
	
	public function extractProperties($inObj){
	
		if (method_exists($this, specialPropertyList)){
			$outArray=$this->specialPropertyList(); //node entities do not have properties corresponding to table, this specifies them explicitly
		}
		else{
			$objArray=(array)$inObj;
			$outArray=array();

			foreach ($objArray as $label=>$data){
				$label=preg_replace('/.*\\x00(.*)$/', '\1', $label);
				$outArray[$label]=true;
			}
		}

		return $outArray;
	}


public function writeDb($recListArray){

		$dbResult=$this->updateOrInsert($recListArray);
		
	
	return $dbResult;

}//end of method
	
public function updateOrInsert($recArray){
	//http://framework.zend.com/manual/1.12/en/zend.db.select.html
	//echo "get_class=".get_class($select)."<br>";
	
	
	$db=$this->getDbConnection();

	$helixRefIdList=\Q\Utils::intoSimpleArray($recArray, 'refId');
	$helixRefIdList[]=time();
	
	$select = $db->select();
	$select->from($this->getTableName());
	$select->where('refId IN(?)', $helixRefIdList);
	$stmt = $db->query($select);
	$result = $stmt->fetchAll();
	

	$alreadyInDbRefIdList=\Q\Utils::intoSimpleArray($result, 'refId');

	$notInDbRefIdList=array_diff($helixRefIdList, $alreadyInDbRefIdList);

	$updateList=\Q\Utils::filterAllowed($recArray, 'refId', $alreadyInDbRefIdList);
	$insertList=\Q\Utils::filterAllowed($recArray, 'refId', $notInDbRefIdList);

	$this->updateDb($updateList);
	$this->insertDb($insertList);

	
	return "base model::updateOrInsert() says tableName= ".$this->getTableName()."";
	}

private function updateDb($recList){

		$db=$this->getDbConnection();
		$tableName=$this->getTableName();
		
		foreach ($recList as $label=>$data){
			if (method_exists($this, 'convertHelixData')){

					$data=$this->convertHelixData($data);

			}
			
			error_log("HACK: overwriting incoming 'created' data");
			$tmp=new \DateTime(date("Y-m-d H:i:s"));
			$data['created']=$tmp->format("Y-m-d H:i:s");
			
			
			$tmp=new \DateTime(date("Y-m-d H:i:s"));
			$data['modified']=$tmp->format("Y-m-d H:i:s");
			

			$data=$this->unsetSpecialFieldNames($data);
			
				global $doNotFinishTickle;
				global $dbWriteErrorMessages;
				global $dbWriteSuccessMessages;
			
			try{
			$db->update($tableName, $data, "refId = '{$data['refId']}'");
			}
			catch(Exception $e){
				error_log("BAD RECORD SKIPPED UPDATE $tableName ".$data['refId']." (".$e->getMessage().")");
				$dbWriteErrorMessages.="<div>BAD RECORD SKIPPED (update) $tableName refId:".$data['refId']." <div style='margin-left:20pt;font-size:70%;'>(".$e->getMessage().")</div></div>";

				$doNotFinishTickle=true;
				
			}
			
		}

error_log("UPDATED ".count($recList)." records (minus any errors) in $tableName<div>");
$dbWriteSuccessMessages.="<div>UPDATED ".count($recList)." records (minus any errors) in <b>$tableName</b><div>";


}

private function insertDb($recList){

		$db=$this->getDbConnection();
		$tableName=$this->getTableName();
		
		
		foreach ($recList as $label=>$data){
			if (method_exists($this, 'convertHelixData')){

					$data=$this->convertHelixData($data);

			}
			
			$tmp=new \DateTime(date("Y-m-d H:i:s"));
			$data['modified']=$tmp->format("Y-m-d H:i:s");
			
			$tmp=new \DateTime(date("Y-m-d H:i:s"));
			$data['created']=$tmp->format("Y-m-d H:i:s");
			
				
				global $doNotFinishTickle;
				global $dbWriteErrorMessages;
				global $dbWriteSuccessMessages;

			$data=$this->unsetSpecialFieldNames($data);
			try{
			$db->insert($tableName, $data);
			}
			catch(Exception $e){
				error_log("BAD RECORD SKIPPED INSERT $tableName ".$data['refId']);
				$dbWriteErrorMessages.="<div>BAD RECORD SKIPPED (insert) $tableName refId:".$data['refId']." <div style='margin-left:20pt;font-size:70%;'>(".$e->getMessage().")</div></div>";
				echo("<div>BAD RECORD SKIPPED INSERT $tableName ".$data['refId']."</div>");
				$doNotFinishTickle=true;
				
			}

		}
error_log(" INSERTED ".count($recList)." records (minus any errors) into >$tableName");
$dbWriteSuccessMessages.="<div style='color:green;font-size:18pt;'>INSERTED ".count($recList)." records (minus any errors) into <b>$tableName</b><div>";

}

private function getDbConnection(){

	if (!isset(self::$dbConnection)){
		$specs=Zend_Registry::get('databaseSpecs');

		$db = new Zend_Db_Adapter_Pdo_Mysql(array(
			'host'     => $specs['host'],
			'username' => $specs['user'],
			'password' => $specs['password'],
			'dbname'   => $specs['dbname']
		));		
		
		self::$dbConnection=$db;
		
	}
	
	return self::$dbConnection;
	
}//end of method

protected function helixToDate($value){
	if (!$value){
		error_log("HACK: empty date value for helix object, forced to 2000-01-01");
		//this should be revised to something that makes sense
		return '2000-01-01';
	}
	$valueBits=explode('/', $value);
	
	$year=$valueBits[2];
	if ($year<2000){
		$year=$year+2000;
	}
	$month=str_pad($valueBits[0], 2, '0', STR_PAD_LEFT);
	$day=str_pad($valueBits[1], 2, '0', STR_PAD_LEFT);
	$dateString="$year-$month-$day";

	return $dateString;
}

public function purgeInactive(){
		$db=$this->getDbConnection();		
		$tableName=$this->getTableName();
 		$count=$db->delete($tableName, "isActiveFlag='0'");
		return "Deleted $count records from $tableName";
}
	
	private function getTableName(){
		if (!isset($this->entity)){ $this->generate();}
		$entity=$this->entity;
		return $entity::tableName;
	}


}//end of class

