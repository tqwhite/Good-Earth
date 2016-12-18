<?php
namespace Heliport;

class OutputManager
{

private $connection;
private $className;

public function __construct(){
	$this->className=get_class($this);
	$this->initHelix();
}

public function __destruct(){
	$this->connection->releasePoolUser();
}

private function initHelix(){
	$this->connection=new ServerInterface();
	$helix_status = $this->connection->ihr190();
// 	if (!$this->helix_status){
// 		die("<div style='color:red;font-size:24pt;'>OutputManager says: Helix is down</div>");
// 	}
	$this->connection->leasePoolUser();
}

public function setBatchId(){
	$batchId=time();
	
	error_log("OutputManager::setBatchId() - STARTING - batchId=$batchId");	

	$this->connection->store(
		'  inert process',
		'exportBatchTimestampNew',
		array('exportBatchTimestamp'=>$batchId, 'blank2'=>'goodbye')
	);
	error_log("OutputManager::setBatchId() - ENDING - batchId=$batchId");	
	return $batchId;
}

public function writeAndValidate($tableName, $inData){
 		$recordsWrittenReport="<div style='color:gray;margin:5px 0px;' tqdebug><b>$tableName</b> records processed:</div>";  

		$writeResult=$this->write($tableName, $inData);
		$validationList=$this->getValidationList($tableName);

		foreach ($writeResult['statusDetails'] as $label=>$data){	
			
			if (in_array($data['recordData']['refId'], $validationList)){
				$recordsWrittenReport.="<div style='color:green;font-size:80%;margin-left:15px;'>success for \[{$data['recordData']['refId']}] (purchaseRefId={$data['purchaseRefId']})</div>";
				continue;
			}
			
 			$recordsWrittenReport.="<div style='color:orange;font-size:80%;margin-left:15px;'>second try for\[{$data['recordData']['refId']}] (purchaseRefId={$data['purchaseRefId']})</div>";
			
			$secondTryResult=$this->write($tableName, array($data));
			$validationList=$this->getValidationList($tableName);
			

			if (!in_array($data['recordData']['refId'], $validationList)){
 				$recordsWrittenReport.="<div style='color:red;margin-left:15px;font-size:80%;'>FAILED TWICE for\[{$data['recordData']['refId']}] (purchaseRefId={$data['purchaseRefId']})</div>";
				$failedTwiceRecordList[$data['recordData']['purchaseRefId']]=$data;
			}
			
			
		}
		
		return array(
			'validationList'=>$validationList,
			'recordsWrittenReport'=>$recordsWrittenReport,
			'failedTwiceRecordList'=>$failedTwiceRecordList
		);
}

private function getValidationList($tableName){
		$helixList = $this->connection->retrieve(
			"SLN_".$tableName,
			"batchConf_".$tableName
		);
		
		$validationList=\Q\Utils::intoSimpleArray($helixList['data'], 'refId');
		

		return $validationList;
}

public function write($tableName, $inData)
    {
    	$outResult=true;
    	$outString='';
    	$outArray=array();
    	
    	unset($data['purchaseRefId']);

		foreach ($inData as $label=>$data){
			$element=$data;
    		$purchaseRefId=$data['purchaseRefId'];

			$element=$this->removeNulls($element);

			$result = $this->connection->storeAndDisplayFieldsReport( //storeAndDisplayFieldsReport
			"  inert process",
			$tableName,
			$element
			);
		
			$outArray[]=array(
				'tableName'=>$tableName,
				'purchaseRefId'=>$purchaseRefId,
				'recordData'=>$data
			);
			
			$outString.=\Q\Utils::dumpWebString($data, "tableName=$tableName, ".count($data)." records, writeStatus=$result");;
			
		if (isset($result)){
			$outResult=$outResults&&$result;
		}

		}
		return array(
			'messages'=>$outString,
			'statusDetails'=>$outArray
		);
    }
private function removeNulls($inArray){
	$outArray=array();
	foreach ($inArray as $label=>$data){
			if (is_null($data)){
				$outArray[$label]='';
			}
			else{
				$outArray[$label]=$data;
			}
		}
	return $outArray;;
}

}
