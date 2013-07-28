<?php

class Application_Model_GradeSchoolNodes extends Application_Model_Base
{
	const entityName="GradeSchoolNode";
	
	const helixImportRelationName="SLN_gradeSchoolNodes";
	const helixImportViewName="gradeSchoolNodes_Data";

	public function __construct(){
		parent::__construct();
	}

	static function formatDetail($inData, $originsArray){

	return array(
					'temp'=>"this is a utility model for importing only"
				);
	}
	
	protected function specialPropertyList(){
		return array(
		
			'refId'=>true,
			'created'=>true,
			'gradeLevelRefId'=>true,
			'schoolRefId'=>true
		
		
		);
	}

}

