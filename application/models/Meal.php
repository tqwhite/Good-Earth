<?php

class Application_Model_Meal extends Application_Model_Base
{
	const entityName="Meal";

	static function formatScalar($inData, $originsArray){
	return array(
					'name'=>$inData->name,
					'shortName'=>$inData->shortName,
					'description'=>$inData->description,
					'suggestedPrice'=>$inData->suggestedPrice,
					'refId'=>$inData->refId,
				);
	}
}

