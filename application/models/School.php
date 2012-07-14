<?php

class Application_Model_School extends Application_Model_Base
{

	const entityName="School";

	public function __construct(){
		parent::__construct();
	}

	static function formatScalar($inData, $originsArray){
		return array(
			'name'=>$inData->name,
			'refId'=>$inData->refId,
			'gradeLevels'=>\Application_Model_GradeLevel::formatOutput($inData->gradeLevelNodes)
		);
	}

}

