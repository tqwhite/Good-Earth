<?php

class Application_Model_Day extends Application_Model_Base
{
	const entityName="Day";

	public function __construct(){
		parent::__construct();
	}

	static function formatDetail($inData, $originsArray){

	return array(
					'title'=>$inData->day->title,
				'seqNum'=>$inData->day->seqNum,
					'refId'=>$inData->day->refId
				);
	}

}

