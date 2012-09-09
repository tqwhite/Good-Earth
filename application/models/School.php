<?php

class Application_Model_School extends Application_Model_Base
{

	const entityName="School";

	public function __construct(){
		parent::__construct();
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
	static function formatDetail($inData, $originsArray){
		if ($inData->school){
			return array(
				'name'=>$inData->school->name,
				'refId'=>$inData->school->refId,
				'suppressDisplay'=>$inData->school->suppressDisplay,
				'gradeLevels'=>\Application_Model_GradeLevel::formatOutput($inData->school->gradeLevelNodes)
			);
		}
		else{
			return array(
				'name'=>$inData->name,
				'refId'=>$inData->refId,
				'suppressDisplay'=>$inData->suppressDisplay,
				'gradeLevels'=>\Application_Model_GradeLevel::formatOutput($inData->gradeLevelNodes)
			);
		}
	}

}

