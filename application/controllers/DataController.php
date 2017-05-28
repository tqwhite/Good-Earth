<?php

class DataController extends Q_Controller_Base
{

    public function init()
    { 
        
        $this->_helper->_layout->setLayout('not_store');

    }

    public function indexAction()
    {
        // action body
    }

    public function testAction()
    {
        
        
		$importObj=new \Application_Model_Import();
		$resultData=$importObj->execute();

        $this->view->message=$resultData;
    }

    public function importAction()
    {
		error_log("Data/importAction() - STARTED================================");
        $this->_helper->_layout->setLayout('not_store');
        
        
		$importObj=new \Application_Model_Import();
		$resultData=$importObj->execute();

        $this->view->message=$resultData;
		error_log("Data/importAction() - FINISHED================================");
    }
    
    private function sendFailedTransmissionEmail($mailFailList){
    
    
		$tr=new Zend_Mail_Transport_Sendmail();
		Zend_Mail::setDefaultTransport($tr);
		Zend_Mail::setDefaultFrom('school@genatural.com', "Good Earth Lunch Program");
		Zend_Mail::setDefaultReplyTo('school@genatural.com', "Good Earth Lunch Program");
		$addressList[]=array('name'=>'Website Programmer', 'address'=>'tq@justkidding.com', 'type'=>'accounting');

		$emailMessage=\Q\Utils::dumpWebString($mailFailList, "mailFailList");
		$emailSubject="Good Earth Website Order Transmission Error Notification";

		for ($i=0, $len=count($addressList); $i<$len; $i++){
			$element=$addressList[$i];
			$mail = new Zend_Mail();
			$mail->setSubject($emailSubject);
			$mail->setBodyHtml($emailMessage);

			$mail->addTo($element['address'], $element['name']);

			$mail->send($tr);

		}

		Zend_Mail::clearDefaultFrom();
		Zend_Mail::clearDefaultReplyTo();

    
    	return count($mailFailList);
    }

	public function thresholdAction(){

	$inputManager=new \Heliport\InputManager();

	$threshold='7-1-10  20:29:27';
	$threshold='7-1-13  20:29:27';
	$threshold='8-4-13  20:29:27';
	$inputManager->setHelixExportThreshold($threshold);

//	$inputManager->releasePoolUsers();

	echo "<div style='color:black;'>threshold={$threshold}</div>";
	exit;
	}

    public function exportAction()
    {
  		$result=$this->exportToHelix('purchases');
    	$summaryString.=$result['summaryReport'];
    	$detailString.=$result['detailReport'];
 
    	$result=$this->exportToHelix('accounts');
    	$summaryString.=$result['summaryReport'];
    	$detailString.=$result['detailReport'];
    
    	$summaryHeader="<div style='margin:25px 0px;font-size:125%;font-weight:bold;font-family:sans-serif;background:#ccc;color:white;padding:5px;'>Details</div>";
    	$detailString="<div style='font-family:sans-serif;'>$detailString</div>";
    	$detailString.="<div style='color:gray;font-size:80%;margin-top:5px;margin-left:15px;'>Note: Accounts, User and Students are sent twice to capture those who registered but did not purchase.</div>";
		$this->view->message=$summaryString.$summaryHeader.$detailString;
    }
    
	private function exportToHelix($categoryName){

		error_log("Data/export $categoryName - STARTING ================================");
    
		$doctrineContainer=Zend_Registry::get('doctrine');
		$em=$doctrineContainer->getEntityManager();
		$entityManager=$em;

		$exportObj=new \Application_Model_Export();
		switch($categoryName){
		case 'accounts':
				$exportableObject=$exportObj->collectPurchases($categoryName);
				$dataList=$exportableObject['exportData'];
		break;
		case 'purchases':
				$exportableObject=$exportObj->collectPurchases($categoryName);
				$dataList=$exportableObject['exportData'];
		break;
		default:
			error_log("exportToHelix() says, Bad categoryName");
			die("exportToHelix() says, Bad categoryName. NO export. No contact with Helix (no pool user consumed)");
		}

		$recCount=count($dataList);
		if ($recCount>0){
			error_log("exportToHelix/$categoryName - FOUND $recCount records for Helix.");

			$result=$exportObj->writeAndValidate($dataList, $categoryName);

		}
		else{
			error_log("exportToHelix/$categoryName - NO NEW DATA IS READY FOR HELIX. NOTHING SENT.");
			$result=array(
				"recordsWrittenReport"=>"NO NEW $categoryName DATA IS READY FOR HELIX. NOTHING SENT.<p/>\n\n"
			);
		}

			$mailFailList=array();
  

			foreach ($exportableObject['entityList'] as $exportItem){
					if (isset($result['failedTwiceRecordList'][$exportItem->refId])){
						continue;
					}
  					$exportItem->alreadyInHelix=true;
  					$entityManager->persist($exportItem);
  
			}

			$entityManager->flush();

			foreach ($exportableObject['exportData'] as $exportItem){
					if (isset($result['failedTwiceRecordList'][$exportItem['refId']])){
						$mailFailList[]=$exportItem;
					}
			}

		if (count($mailFailList)>0){
			$mailResult=$this->sendFailedTransmissionEmail($mailFailList);
			$failTwiceMessage="<div style='background:yellow;color:red;padding:5px;margin-top:15px;'>Some $categoryName records failed twice. Email sent. See details below. <span style='font-size:80%;'>".date("Y-m-d H:i:s")."</span></div>";
		}
		else{
			$failTwiceMessage="<div style='background:green;color:#ddd;;font-weight:bold;padding:5px;margin-top:15px;'>All <span style='color:white;'>$categoryName</span> records sent successfully to Helix. No records failed twice. <span style='font-size:80%;'>".date("Y-m-d H:i:s")."</span></div>";
		}

		$outString="<div style='font-family:sans-serif;'>
			$failTwiceMessage
			{$result['recordsWrittenSummaryReport']}
			</div>\n";

		error_log("Data/$categoryName - FINISHED================================");

		return array(
			summaryReport=>$outString,
			detailReport=>$result['recordsWrittenReport']

		);;
}

}

