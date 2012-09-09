<?php
namespace Heliport;

class OutputManager
{

private $connection;

public function __construct(){
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

public function write($tableName, $inData)
    {
    	$outResult=true;

		foreach ($inData as $label=>$data){
			$element=$data;

			$element=$this->removeNulls($element);

			$result = $this->connection->storeAndDisplayFieldsReport( //storeAndDisplayFieldsReport
			"  inert process",
			$tableName,
			$element
			);

		if (isset($result)){
			$outResult=$outResults&&$result;
		}

		}
		return $outResult;
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
