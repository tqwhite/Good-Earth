<?php

class Application_Model_OfferingDayNodes extends Application_Model_Base
{
	const entityName="OfferingDayNode";
	
	const helixImportRelationName="SLN_offeringsCombined";
	const helixImportViewName="offeringDayNodes_Data";

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
			'dayRefId'=>true
		
		
		);
	}
	
	protected function convertHelixData($data){
		$data['isActiveFlag']=$data['active?']; unset($data['active?']); unset($data['active?']);
		return $data;
	}

}

