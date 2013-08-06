<?php

class Application_Model_Offering extends Application_Model_Base
{
	const entityName="Offering";

	const helixImportRelationName="SLN_offeringsCombined";
	const helixImportViewName="offerings_Data";

	public function __construct(){
		parent::__construct();
	}

	static function XXXformatScalar($inData, $originsArray){

	return array(
					'name'=>$inData->name,
					'refId'=>$inData->refId,
					'created'=>$inData->created,
					'price'=>$inData->price,
					'test'=>$inData->gradeLevelNodes->temp,
					'meal'=>\Application_Model_Meal::formatOutput($inData->meal),
					'gradeLevels'=>array(
							array('title'=>'Second', refId=>'Second'),
							array('title'=>'Third', refId=>'Third'),
							array('title'=>'Fourth', refId=>'Fourth'),
							array('title'=>'Fifth', refId=>'Fifth')
					),
					'days'=>array(
						array(title=>'Mon', refId=>'1'),
						array(title=>'Tues', refId=>'2'),
						array(title=>'Thurs', refId=>'4')
					),
					'schools'=>array(
						array('name'=>'Cascade Canyon', refId=>'SaintMarks'),
						array('name'=>'Marin Horizon', refId=>'MarinHorizon'),
						array('name'=>'St Anselm', refId=>'StAnselm'),
						array('name'=>'Hall Middle School', refId=>'HallMiddleSchool')
					)
				);
	}

	static function formatScalar($inData, $originsArray){

	return array(
					'name'=>$inData->name,
					'refId'=>$inData->refId,
					'created'=>$inData->created,
					'price'=>$inData->price,
					'perYearFull'=>$inData->perYearFull,
					'test'=>$inData->gradeLevelNodes->temp,
					'meal'=>\Application_Model_Meal::formatOutput($inData->meal),
					'gradeLevels'=>\Application_Model_GradeLevel::formatOutput($inData->gradeLevelNodes, 'limited'),
					'days'=>\Application_Model_Day::formatOutput($inData->dayNodes),
					'schools'=>\Application_Model_School::formatOutput($inData->schoolNodes)
				);
	}

	public function newFromArrayList($source, $suppressFlush){ //overrides base.newFromArrayList()


		$outArray=array();

		if (!isset($suppressFlush)){ $suppressFlush=false; }

		foreach ($source as $objArray){
			$this->generate();

				foreach ($objArray as $label=>$data){
					switch($label){
						case 'school':
						case 'schoolRefId':
							if (gettype($data)=='array'){
								foreach ($data as $data2){
									$this->addSchool($data2);
								}
							}
							else{
								$this->addSchool($data);
							}
							break;
						case 'gradeLevelRefId':
						case 'gradeLevel':
							if (gettype($data)=='array'){
								foreach ($data as $data2){
									$this->addGradeLevel($data2);
								}
							}
							else{
								$this->addGradeLevel($data);
							}
							break;
						case 'dayRefId':
						case 'day':
							if (gettype($data)=='array'){
								foreach ($data as $data2){
									$this->addDay($data2);
								}
							}
							else{
								$this->addDay($data);
							}
							break;
						case 'mealRefId':
						case 'meal':
							$this->addMeal($data);
							break;
						default:
							$this->entity->$label=$data;
							break;
					}
				} //end of inner (property) loop

			$this->entityManager->persist($this->entity);

			$outArray[]=$this->entity;
		}

		if (!$suppressFlush){
			$this->entityManager->flush();
		}
		return $outArray;
	}

	public function addMeal($inData){
		if (gettype($inData)=='string'){
			$dayObj=new \Application_Model_Meal();
			$inData=$dayObj->getByRefId($inData);
		}
		$this->entity->meal=$inData;
	}

	public function addDay($inData){
		if (gettype($inData)=='string'){
			$dayObj=new \Application_Model_Day();
			$inData=$dayObj->getByRefId($inData);
		}

		$this->addNodeProperty('OfferingDayNode', 'day', $inData);
	}

	public function addSchool($inData){
		if (gettype($inData)=='string'){
			$dayObj=new \Application_Model_School();
			$inData=$dayObj->getByRefId($inData);
		}
		$this->addNodeProperty('OfferingSchoolNode', 'school', $inData);
	}

	public function addGradeLevel($inData){
		if (gettype($inData)=='string'){
			$dayObj=new \Application_Model_GradeLevel();
			$inData=$dayObj->getByRefId($inData);
		}
		$this->addNodeProperty('OfferingGradeLevelNode', 'gradeLevel', $inData);
	}

	private function addNodeProperty($property, $propertyName, $inEntity){

		$entityClassName="GE\\Entity\\{$property}";
		$nodeEntity=new $entityClassName();

		$nodeEntity->$propertyName=$inEntity;
		$nodeEntity->offering=$this->entity;
		$this->entityManager->persist($nodeEntity);
	}
	
	public function convertHelixData($data){
		$data['isActiveFlag']=($data['active?']=='Yes')?1:0; unset($data['active?']);


		$data['perYearFull']=$data['perYear full']; unset($data['perYear full']);
		
		$data['price']=$data['price']*100;

		return $data;
	}

	public function getByCurrPeriod($periodList, $hydrationMode='array'){
		$tmp=implode("','", $periodList);
		$sqlString='(\''.$tmp.'\')';
		
		$query = $this->entityManager->createQuery("SELECT u from GE\\Entity\\{$this->entityName} u WHERE u.perYearFull in $sqlString");

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
}

