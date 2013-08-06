<?php

class Application_Model_Meal extends Application_Model_Base
{
	const entityName="Meal";
	
	const helixImportRelationName="SLN_meals";
	const helixImportViewName="meals_Data";

	static function formatDetail($inData, $originsArray){
	return array(
					'name'=>$inData->name,
					'description'=>$inData->description,
					'refId'=>$inData->refId,
				);
	}
	
	protected function convertHelixData($data){
		$data['isActiveFlag']=($data['active?']=='Yes')?1:0; unset($data['active?']);

		return $data;
	}
}

