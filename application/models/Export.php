<?php

class Application_Model_Export
{

public function collectPurchases(){
error_reporting(E_ALL && ~E_NOTICE); //error_reporting(E_ERROR | E_WARNING | E_PARSE); //error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

	$dataObj=new \Application_Model_Purchase();
	$outList=array();
		$dataList=$dataObj->getHelixSendList('record');

	if ($debug=true){
		$len=0;
	}
	else{
		$len=count($dataList);
	}

	$i=0;
	foreach($dataList as $label=>$element){
		if ($i++>$len){break;}
		$item=\Application_Model_Purchase::formatOutput($element, 'export', 'purchase');
		$outList[]=$item;

	}

	return array(
		'entityList'=>$dataList,
		'exportData'=>$outList
	);
}

public function getTableData($purchaseList, $tableListString){

	$tableList=explode(' ', $tableListString);

	foreach ($purchaseList as $purchaseRec){
		foreach ($tableList as $tableName){
			switch ($tableName){
				case 'accounts':
					$path='accounts'; //there is a database design error that makes this purchase to accounts be many-to-many
					$outList[$tableName][]=$this->extractScalars($purchaseRec, $path);
				break;
				case 'purchaseOrderNodes':
					$path='purchaseOrderNodes';
					$outList[$tableName][]=$this->extractScalars($purchaseRec, $path);
				break;
				case 'accountPurchaseNodes':
					$path='accountPurchaseNodes';
					$outList[$tableName][]=$this->extractScalars($purchaseRec, $path);
				break;
				case 'purchases':
					$path='';
					$outList[$tableName][]=$this->extractScalars($purchaseRec, $path);
				break;
				case 'users':
					$path='accounts.0.users';
					$outList[$tableName][]=$this->extractScalars($purchaseRec, $path);
				break;
				case 'students':
					$path='accounts.0.students';
					$outList[$tableName][]=$this->extractScalars($purchaseRec, $path);
				break;
				case 'orders':
					$path='orders';
					$outList[$tableName][]=$this->extractScalars($purchaseRec, $path);
				break;
			}
		}
	}
	return $outList;
}

private function extractScalars($list, $path){
	$assocArray=array();
	$list=Q\Utils::getFromDottedPath($list, $path);

	foreach ($list as $label=>$data){
		if (gettype($data)!='array' and gettype($data)!='object'){
					$assocArray[$label]=$data;
		}
		else{

			foreach ($data as $label2=>$data2){
				if (gettype($data2)!='array' and gettype($data2)!='object'){
					if (!isset($assocArray[$label2])){
						$assocArray[$label2]=$data2;
					}
				}
			}
		}
	}
	return $assocArray;
}

static function formatOutput($inData, $outputType){

	for ($i=0, $len=count($inData); $i<$len; $i++){
		$element=$inData[$i];

		switch(get_class($element)){
			case 'GE\Entity\PurchaseOrderNode':
				$newRec=array(
					'orderRefId'=>$element->order->refId,
					'purchaseRefId'=>$element->purchase->refId,
					'refId'=>$element->refId,
					'created'=>$element->created

				);

			break;
			case 'GE\Entity\AccountPurchaseNode':
				$newRec=array(
					'accountRefId'=>$element->account->refId,
					'purchaseRefId'=>$element->purchase->refId,
					'refId'=>$element->refId,
					'created'=>$element->created

				);
			break;
		}
	}
	return $newRec;
}


public function write($inData){
	$outputManager=new \Heliport\OutputManager();

	foreach ($inData as $tableName=>$data){
		$result=$outputManager->write($tableName, $data);
	}

	return $result;
}

}

