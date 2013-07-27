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
	$this->connection->leasePoolUser();
}

public function setBatchId(){
	$batchId=time();
	$this->connection->store(
					'  inert process',
					'exportBatchTimestamp',
					array('exportBatchTimestamp'=>$batchId)
				);
	return $batchId;
}

public function writeAndValidate($tableName, $inData){
 		$outString="<div style='color:blue;margin:5px 0px;' tqdebug>{$this->className} says,</div>";  

		$writeResult=$this->write($tableName, $inData);
		$writeStatusDetails=$writeResult['statusDetails'];
		
		$validationList=$this->getValidationList($tableName);
		
if (true && $tableName=='purchaseOrderNodes'){
	unset($validationList[0]);
	unset($validationList[7]);
}
		foreach ($writeStatusDetails as $label=>$data){	
			
			if (in_array($data['recordData']['refId'], $validationList)){
				$outString.="<div style='color:green;'>success on $tableName\[{$data['recordData']['refId']}] (purchaseRefId={$data['purchaseRefId']})</div>";

				continue;
			}
			
 			$outString.="<div style='color:orange;'>second try on $tableName\[{$data['recordData']['refId']}] (purchaseRefId={$data['purchaseRefId']})</div>";
			
			$secondTryResult=$this->write($tableName, array($data));
			$validationList=$this->getValidationList($tableName);
			
if (true && $tableName=='purchaseOrderNodes'){
	unset($validationList[0]);
}
			if (!in_array($data['recordData']['refId'], $validationList)){
 				$outString.="<div style='color:red;'>FAILED TWICE on $tableName\[{$data['recordData']['refId']}] (purchaseRefId={$data['purchaseRefId']})</div>";
				$failedTwice[$data['recordData']['purchaseRefId']]=$data;
			}
			
			
		}
		
		return array(
			'validationList'=>$validationList,
			'messages'=>$outString,
			'failedTwice'=>$failedTwice
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
			
			$outString.=\Q\Utils::dumpWebString($data, "tableName=$tableName, writeStatus=$result");;
			
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
