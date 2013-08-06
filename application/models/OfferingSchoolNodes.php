<?php

class Application_Model_OfferingSchoolNodes extends Application_Model_Base
{
	const entityName="OfferingSchoolNode";
	
	const helixImportRelationName="SLN_offeringsCombined";
	const helixImportViewName="offeringSchoolNodes_Data";

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
			'offeringRefId'=>true,
			'schoolRefId'=>true
		
		
		);
	}
	
	protected function convertHelixData($data){
		$data['isActiveFlag']=($data['active?']=='Yes')?1:0; unset($data['active?']);

		return $data;
	}

}

