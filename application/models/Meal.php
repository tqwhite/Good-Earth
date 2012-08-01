<?php

class Application_Model_Meal extends Application_Model_Base
{
	const entityName="Meal";

	static function formatDetail($inData, $originsArray){
	return array(
					'name'=>$inData->name,
					'description'=>$inData->description,
					'refId'=>$inData->refId,
				);
	}
}

