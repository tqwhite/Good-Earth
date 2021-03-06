<?php

class Application_Model_Day extends Application_Model_Base
{
	const entityName="Day";
	
	const helixImportRelationName="SLN_days";
	const helixImportViewName="days_Data";

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
	
	protected function convertHelixData($data){
		$data['isActiveFlag']=($data['active?']=='Yes')?1:0; unset($data['active?']);
		$data['helixId']=$data['helix id']; unset($data['helix id']);

		return $data;
	}

}

