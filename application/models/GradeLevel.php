<?php

class Application_Model_GradeLevel extends Application_Model_Base
{

	const entityName="GradeLevel";

	public function __construct(){
		parent::__construct();
	}

	static function formatScalar($inData, $originsArray){

		if ($inData->gradeLevel){ //if it comes via School, it uses GradeLevelNode
			$title=$inData->gradeLevel->title;
			$refId=$inData->gradeLevel->refId;
		}
		else{
			$title=$inData->title;
			$refId=$inData->refId;
		}

		return array(
			'descriptor'=>$inData->descriptor,
			'title'=>$title,
			'refId'=>$refId
		);
	}

}

