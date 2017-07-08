<?php

class Application_Model_StudentOrderNodes extends Application_Model_Base
{
	const entityName="StudentOrderNode";
	
	const helixImportRelationName="SLN_ordersCombined";
	const helixImportViewName="StudentOrderNodes_Data";

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
			'studentRefId'=>true,
			'orderRefId'=>true
		
		
		);
	}
	
	protected function convertHelixData($data){
	//	$data['isActiveFlag']=($data['active?']=='Yes')?1:0; unset($data['active?']);

		return $data;
	}

}

