<?php

class Application_Model_Offering extends Application_Model_Base
{
	const entityName="Offering";



	public function __construct(){
		parent::__construct();
	}

	static function XXXformatScalar($inData, $originsArray){

	return array(
					'name'=>$inData->name,
					'refId'=>$inData->refId,
					'created'=>$inData->created,
					'price'=>$inData->price/100,
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
					'price'=>$inData->price/100,
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
			$this->entityManager->persist($this->entity);

			foreach ($objArray as $label=>$data){
				switch($label){
					case 'school':
						if (gettype($data)=='array'){
							foreach ($data as $data2){
								$this->addSchool($data2);
							}
						}
						else{
							$this->addSchool($data);
						}
						break;
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
					case 'meal':
						$this->addMeal($data);
						break;
					default:
						$this->entity->$label=$data;
						break;
				}
			}

			$this->entityManager->persist($this->entity);

			if (!$suppressFlush){
				$this->entityManager->flush();
			}

			$outArray[]=$this->entity;
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
}

