<?php

class Application_Model_Export
{

private $className;

public function __construct(){
	$this->className=get_class($this);
}

public function collectPurchases($categoryName){
error_reporting(E_ALL && ~E_NOTICE); //error_reporting(E_ERROR | E_WARNING | E_PARSE); //error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

	switch($categoryName){
	case 'purchases':
		$dataObj=new \Application_Model_Purchase();
	break;
	case 'accounts':
		$dataObj=new \Application_Model_Account();
	break;
	}
	
	$outList=array();
		$dataList=$dataObj->getHelixSendList('record');

	if ($debug=false){
		$len=0;
	}
	else{
		$len=count($dataList);
	}

	$i=0;
	foreach($dataList as $label=>$element){
		if ($i++>$len){break;}
		
		switch($categoryName){
			case 'purchases':
					$item=\Application_Model_Purchase::formatOutput($element, 'export', 'purchase');
			break;
			case 'accounts':
					$item=\Application_Model_Account::formatOutput($element, 'export', 'purchase');
			break;
		}
		$outList[]=$item;
	}
	return array(
		'entityList'=>$dataList,
		'exportData'=>$outList
	);
}

private function explodeAccountsList($accountList, $tableListString){

	$tableList=explode(' ', $tableListString);
	foreach ($accountList as $accountRec){
		$purchaseRefId=$accountRec['refId'];
		foreach ($tableList as $tableName){
			switch ($tableName){
				case 'accounts':
					$path=''; //there is a database design error that makes this purchase to accounts be many-to-many
					$this->extractScalars($outList[$tableName], $accountRec, $path, $purchaseRefId);
				break;
				case 'users':
					$path='users';
					$this->extractScalars($outList[$tableName], $accountRec, $path, $purchaseRefId);
				break;
				case 'students':
					$path='students';
					$this->extractScalars($outList[$tableName], $accountRec, $path, $purchaseRefId);
				break;
			}
		}
	}
	return $outList;
}

private function explodePurchasesList($purchaseList, $tableListString){

	$tableList=explode(' ', $tableListString);
	foreach ($purchaseList as $purchaseRec){
		$purchaseRefId=$purchaseRec['refId'];
		foreach ($tableList as $tableName){
			switch ($tableName){
				case 'accounts':
					$path='accounts'; //there is a database design error that makes this purchase to accounts be many-to-many
					$this->extractScalars($outList[$tableName], $purchaseRec, $path, $purchaseRefId);
				break;
				case 'purchaseOrderNodes':
					$path='purchaseOrderNodes';
					$this->extractScalars($outList[$tableName], $purchaseRec, $path, $purchaseRefId);
				break;
				case 'accountPurchaseNodes':
					$path='accountPurchaseNodes';
					$this->extractScalars($outList[$tableName], $purchaseRec, $path, $purchaseRefId);
				break;
				case 'purchases':
					$path='';
					$this->extractScalars($outList[$tableName], $purchaseRec, $path, $purchaseRefId);
				break;
				case 'users':
					$path='accounts.0.users';
					$this->extractScalars($outList[$tableName], $purchaseRec, $path, $purchaseRefId);
				break;
				case 'students':
					$path='accounts.0.students';
					$this->extractScalars($outList[$tableName], $purchaseRec, $path, $purchaseRefId);
				break;
				case 'orders':
					$path='orders';
 					$this->extractScalars($outList[$tableName], $purchaseRec, $path);
				break;
			}
		}
	}
	return $outList;
}

private function extractScalars(&$outTable, $list, $path, $purchaseRefId){
	$list=Q\Utils::getFromDottedPath($list, $path);
	$isList=true;

	foreach($list as $label=>$data){
		if (gettype($data)!='array' and gettype($data)!='object'){
			$isList=false;
			break;
		}
	}

	if (!$isList){
		$assocArray=array('purchaseRefId'=>$purchaseRefId);
		foreach ($list as $label=>$data){
			if (gettype($data)!='array' and gettype($data)!='object'){
				if (!isset($assocArray[$label])){
					$assocArray[$label]=$data;
				}
			}
		}
		$outTable[]=$assocArray;
	}
	else{
		foreach ($list as $label=>$data){
			$assocArray=array('purchaseRefId'=>$purchaseRefId);
			foreach ($data as $label2=>$data2){
				if (gettype($data2)!='array' and gettype($data2)!='object'){
					if (!isset($assocArray[$label2])){
						$assocArray[$label2]=$data2;
					}
				}
			}
			$outTable[]=$assocArray;
		}
	}
}

static function formatOutput($inData, $outputType, $flag){
	$newRec=array();
	for ($i=0, $len=count($inData); $i<$len; $i++){
		$element=$inData[$i];

		switch(get_class($element)){
			case 'GE\Entity\PurchaseOrderNode':
				$newRec[]=array(
					'orderRefId'=>$element->order->refId,
					'purchaseRefId'=>$element->purchase->refId,
					'refId'=>$element->refId,
					'created'=>$element->created

				);

			break;
			case 'GE\Entity\AccountPurchaseNode':
				$newRec[]=array(
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

public function writeAndValidate($dataList, $categoryName){
	switch ($categoryName){
	case 'purchases':
		$tableArray=$this->explodePurchasesList($dataList, 'accounts users students orders purchases accountPurchaseNodes purchaseOrderNodes');
	break;
	case 'accounts':
		$tableArray=$this->explodeAccountsList($dataList, 'accounts users students');
	break;
	}
	$resultArray=$this->executeWriteAndValidate($tableArray);
	
	$resultArray['recordsWrittenReport']=$resultArray['recordsWrittenReport'];
	$summaryHeaderString=$this->generateListings($tableArray, $categoryName);
	$resultArray['recordsWrittenSummaryReport']=$summaryHeaderString;
	
	return $resultArray;
}

private function executeWriteAndValidate($inData){

	$recordsWrittenReport='';
	$outputManager=new \Heliport\OutputManager();
	$batchId=$outputManager->setBatchId();
	$failedTwiceRecordList=array();
	foreach ($inData as $tableName=>$data){
		$result=$outputManager->writeAndValidate($tableName, $data);
		$recordsWrittenReport.=$result['recordsWrittenReport'];
		if (is_array($result['failedTwiceRecordList'])){
			$failedTwiceRecordList=array_merge($failedTwiceRecordList, $result['failedTwiceRecordList']);
		}
	}
	return array('recordsWrittenReport'=>$recordsWrittenReport, 'failedTwiceRecordList'=>$failedTwiceRecordList);
}

private function generateListings($tableArray, $categoryName){
	$outString="<div style='margin:15px 0px 0px 20px;color:gray;'>";
	switch($categoryName){
		case 'accounts':
			$outString.="account records sent: ".count($tableArray['accounts'])."<BR>";
			$outString.="user records sent: ".count($tableArray['users'])."<BR>";
			$outString.="student records sent: ".count($tableArray['students'])."<BR>";
		break;
		case 'purchases':
			$outString.="purchase records sent: ".count($tableArray['purchases'])."<BR>";
			$outString.="order records sent: ".count($tableArray['orders'])."<BR>";
		break;
	}
		$outString.="</div>";
		return $outString;
}

}

