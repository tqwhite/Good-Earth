<?php

class Application_Model_School extends Application_Model_Base
{

	const entityName="School";
	
	const helixImportRelationName="SLN_schools";
	const helixImportViewName="schools_Data";

	public function __construct(){
		parent::__construct();
	}


	static function formatScalar($inData, $outputType){

		foreach ($inData as $label=>$data){
			$hasZeroProperty=true;
		}

		if ($hasZeroProperty){
			$inData=$inData[0];
		}

		$outArray=static::formatDetail($inData, $outputType);
		if ($hasZeroProperty){
			return array($outArray);
		}
		else{
			return $outArray;
		}

	}
	static function formatDetail($inData, $originsArray){
		if ($inData->school){
			return array(
				'name'=>$inData->school->name,
				'refId'=>$inData->school->refId,
				
				'currPeriod'=>$inData->school->currPeriod,
				'dateOrderingEnd'=>$inData->school->dateOrderingEnd,
				'dateOrderingBegin'=>$inData->school->dateOrderingBegin,
				'datePeriodEnd'=>$inData->school->datePeriodEnd,
				'datePeriodBegin'=>$inData->school->datePeriodBegin,
				'merchantAccountId'=>$inData->school->merchantAccountId,
				
				'isActiveFlag'=>$inData->isActiveFlag,
				'suppressDisplay'=>$inData->school->suppressDisplay,
				'gradeLevels'=>\Application_Model_GradeLevel::formatOutput($inData->school->gradeLevelNodes)
			);
		}
		else{
			return array(
				'name'=>$inData->name,
				'refId'=>$inData->refId,
				
				'currPeriod'=>$inData->currPeriod,
				'dateOrderingEnd'=>$inData->dateOrderingEnd,
				'dateOrderingBegin'=>$inData->dateOrderingBegin,
				'datePeriodEnd'=>$inData->datePeriodEnd,
				'datePeriodBegin'=>$inData->datePeriodBegin,
				'merchantAccountId'=>$inData->merchantAccountId,
				
				'isActiveFlag'=>$inData->isActiveFlag,
				'suppressDisplay'=>$inData->suppressDisplay,
				'gradeLevels'=>\Application_Model_GradeLevel::formatOutput($inData->gradeLevelNodes)
			);
		}
	}
	
	public function isAnyoneOpen(){
		$schools=$this->getList();
		
		$now=new \DateTime();
		$oneDay=new \DateInterval('P1D');
		
		$schoolCount=count($schools);
		
		for ($i=0, $len=$schoolCount; $i<$len; $i++){
			$element=$schools[$i];
			
			$begin=$element['dateOrderingBegin'];
			$end=$element['dateOrderingEnd'];
			$end=$end->add($oneDay);
			
			$open=($now>$begin && $now<$end);
			if (!$open){
				$schoolCount--;
			}
			
		}
		
		If ($schoolCount===0){
			return false;
		}
		else{
			return true;	
		}
	}
	
	protected function convertHelixData($data){
		$data['isActiveFlag']=($data['active?']=='Yes')?1:0; unset($data['active?']);

		$data['dateOrderingBegin']=$this->helixToDate($data['dateOrderingBegin']);
		$data['dateOrderingEnd']=$this->helixToDate($data['dateOrderingEnd']);
		$data['datePeriodBegin']=$this->helixToDate($data['datePeriodBegin']);
		$data['datePeriodEnd']=$this->helixToDate($data['datePeriodEnd']);
		$data['helixId']=$data['helix id']; unset($data['helix id']);

		return $data;
	}

}

