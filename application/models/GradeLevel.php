<?php

class Application_Model_GradeLevel extends Application_Model_Base
{

	const entityName="GradeLevel";
	
	const helixImportRelationName="SLN_gradeLevels";
	const helixImportViewName="gradeLevels_Data";

	public function __construct(){
		parent::__construct();
	}



	static function formatDetail($inData, $outputType){
			if ($inData->gradeLevel){ $inData=$inData->gradeLevel;}
			$title=$inData->title;
			$refId=$inData->refId;

			$outArray=array(
				'title'=>$title,
				'seqNum'=>$inData->seqNum,
				'refId'=>$refId
			);

			return $outArray;
	}




	public function getByRefId($refId){

		$query = $this->entityManager->createQuery("SELECT u from GE\\Entity\\{$this->entityName} u WHERE u.refId = :refId");
		$query->setParameters(array(
			'refId' => $refId
		));
		$list = $query->getResult();
		$this->entity=$list[0];
		return $this->entity;

	}

}

