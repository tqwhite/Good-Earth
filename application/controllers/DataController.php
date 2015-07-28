<?php

class DataController extends Q_Controller_Base
{

    public function init()
    { 
        parent::updateAuditInfo($this->getFileName());
        $this->_helper->_layout->setLayout('not_store');
        
        $tmp=parent::getAuditInfo();

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

//			$addressList[]=array('name'=>'Good Earth Organic School Lunch Program', 'address'=>'school@genatural.com', 'type'=>'accounting');

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
    	$outString='';
    	$outString.=$this->exportPurchases();
    	$outString.=$this->exportAccounts();
    	
    	
		$this->view->message=$outString;
    }
	private function exportAccounts(){
		
		error_log("Data/exportAccounts() - STARTING ================================");	

    	$outString='';
    
		$doctrineContainer=Zend_Registry::get('doctrine');
		$em=$doctrineContainer->getEntityManager();
		$entityManager=$em;


//     	$exportObj=new \Application_Model_Export();
// 		$exportableObject=$exportObj->collectPurchases('Application_Model_Purchase');
// 		$dataList=$exportableObject['exportData'];

    	$exportObj=new \Application_Model_Export();
		$exportableObject=$exportObj->collectPurchases('Application_Model_Account');
		$dataList=$exportableObject['exportData'];

		if (count($dataList)>0){
		

			$result=$exportObj->writeAndValidate($dataList, 'accounts');
	
			$writeMessages=$result['messages'];
			
			$failedListString=\Q\Utils::dumpWebString($result['failedTwice'], "result['failedTwice']");

		}
		else{
			$outString.="NO NEW DATA IS READY FOR HELIX. NOTHING SENT.<p/>\n\n";
		error_log("Data/exportAccounts() - NO NEW DATA IS READY FOR HELIX. NOTHING SENT.");	
			$result=true;
		}


		if ($result){
			$outString.="export date".date("Y-m-d H:i:s")."<p/>\n\n\n";
			$outString.="start of accountId list <br/>\n";
			$sucessListString='';
			
			$mailFailList=array();

			foreach ($exportableObject['entityList'] as $exportItem){

					if (isset($result['failedTwice'][$exportItem->refId])){
						continue;
					}


  					$exportItem->alreadyInHelix=true;
  					$entityManager->persist($exportItem);
  					$sucessListString.="{$exportItem->refId}<br/>\n";
 					$outString.="exportRefId {$exportItem->refId}<br/>\n";
			}
			$entityManager->flush();

			foreach ($exportableObject['exportData'] as $exportItem){
					if (isset($result['failedTwice'][$exportItem['refId']])){
						$mailFailList[]=$exportItem;
					}
			}

			$outString.="end of accountId list <p/>\n\n\n";
		}
		else{
			$outString.="HELIX ERROR, purchases will all be resent next time<p/>\n\n";
		}
		
		if (count($mailFailList)>0){
			$mailResult=$this->sendFailedTransmissionEmail($mailFailList);
		}

		$outString="<DIV style='font-size:200%;color:red;'>EXPORTING ACCOUNTS===================</div>
			sent email concering $mailResult failed purchase transmissions<br/>
			failedListString=$failedListString
			sucessListString=$sucessListString
			outString=$outString
			$writeMessages
			<div style='color:red;'>end ACCOUNTS transcript</div>\n";
		
		error_log("Data/exportAccounts() - FINISHED================================");	
		return $outString;

    
	}

	private function exportPurchases(){
		
		error_log("Data/exportPurchases() - STARTING ================================");	

    	$outString='';
    
		$doctrineContainer=Zend_Registry::get('doctrine');
		$em=$doctrineContainer->getEntityManager();
		$entityManager=$em;


    	$exportObj=new \Application_Model_Export();
		$exportableObject=$exportObj->collectPurchases('Application_Model_Purchase');
		$dataList=$exportableObject['exportData'];

//     	$exportObj=new \Application_Model_Export();
// 		$exportableObject=$exportObj->collectPurchases('Application_Model_Account');
// 		$dataList=$exportableObject['exportData'];

		if (count($dataList)>0){
		

			$result=$exportObj->writeAndValidate($dataList, 'purchases');	
	
			$writeMessages=$result['messages'];
			
			$failedListString=\Q\Utils::dumpWebString($result['failedTwice'], "result['failedTwice']");

		}
		else{
			$outString.="NO NEW DATA IS READY FOR HELIX. NOTHING SENT.<p/>\n\n";
		error_log("Data/exportPurchases() - NO NEW DATA IS READY FOR HELIX. NOTHING SENT.");	
			$result=true;
		}


		if ($result){
			$outString.="export date".date("Y-m-d H:i:s")."<p/>\n\n\n";
			$outString.="start of purchaseId list <br/>\n";
			$sucessListString='';
			
			$mailFailList=array();

			foreach ($exportableObject['entityList'] as $exportItem){

					if (isset($result['failedTwice'][$exportItem->refId])){
						continue;
					}

  					$exportItem->alreadyInHelix=true;
  					$entityManager->persist($exportItem);
  					$sucessListString.="{$exportItem->refId}<br/>\n";
 					$outString.="exportRefId {$exportItem->refId}<br/>\n";
			}
			$entityManager->flush();

			foreach ($exportableObject['exportData'] as $exportItem){
					if (isset($result['failedTwice'][$exportItem['refId']])){
						$mailFailList[]=$exportItem;
					}
			}

			$outString.="end of purchaseId list <p/>\n\n\n";
		}
		else{
			$outString.="HELIX ERROR, purchases will all be resent next time<p/>\n\n";
		}
		
		if (count($mailFailList)>0){
			$mailResult=$this->sendFailedTransmissionEmail($mailFailList);
		}

		$outString="<DIV style='font-size:200%;color:red;'>EXPORTING PURCHASES===================</div>
			sent email concering $mailResult failed purchase transmissions<br/>
			failedListString=$failedListString
			sucessListString=$sucessListString
			outString=$outString
			$writeMessages
			<div style='color:red;'>end PURCHASES transcript</div>\n";
		
		error_log("Data/exportPurchases() - FINISHED================================");	
		
		return $outString;

    
	}

}





