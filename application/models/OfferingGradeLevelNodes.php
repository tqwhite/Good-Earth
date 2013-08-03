<?php

class Application_Model_OfferingGradeLevelNodes extends Application_Model_Base
{
	const entityName="OfferingGradeLevelNode";
	
	const helixImportRelationName="SLN_offeringsCombined";
	const helixImportViewName="offeringGradeLevelNodes_Data";

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
			'gradeLevelRefId'=>true
		
		
		);
	}
	
	protected function convertHelixData($data){
		$data['isActiveFlag']=$this->helixToDate($data['active?']);
		return $data;
	}

}

