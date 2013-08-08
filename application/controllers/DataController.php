<?php

class DataController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->_layout->setLayout('not_store');
        /* Initialize action controller here */
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

    public function exportAction()
    {
    
    	$outString='';
    
		$doctrineContainer=Zend_Registry::get('doctrine');
		$em=$doctrineContainer->getEntityManager();
		$entityManager=$em;


    	$exportObj=new \Application_Model_Export();
		$purchaseData=$exportObj->collectPurchases();
		$dataList=$purchaseData['exportData'];

		if (count($dataList)>0){
		

			$result=$exportObj->writeAndValidate($dataList);		
			$outString.=$result['messages'];
			
			$failedListString=\Q\Utils::dumpWebString($result['failedTwice'], "result['failedTwice']");

		}
		else{
			$outString.="NO NEW DATA IS READY FOR HELIX. NOTHING SENT.<p/>\n\n";
			$result=true;
		}


		if ($result){
			$outString.="Exported to helix ".date("Y-m-d H:i:s")."<p/>\n\n\n";
			$outString.="start of purchaseId list <br/>\n";
			$sucessListString='';
			
			$mailFailList=array();

			foreach ($purchaseData['entityList'] as $purchase){

					if (isset($result['failedTwice'][$purchase->refId])){
						continue;
					}

  					$purchase->alreadyInHelix=true;
  					$entityManager->persist($purchase);
  					$sucessListString.="{$purchase->refId}<br/>\n";
 					$outString.="purchaseRefId {$purchase->refId}<br/>\n";
			}
			$entityManager->flush();

			foreach ($purchaseData['exportData'] as $purchase){
					if (isset($result['failedTwice'][$purchase['refId']])){
						$mailFailList[]=$purchase;
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

		$outString="<div style='color:red;'>start transcript</div>\n\n
			sent email concering $mailResult failed purchase transmissions<br/>
			failedListString=$failedListString
			sucessListString=$sucessListString
			outString=$outString
			<div style='color:red;'>end transcript</div>\n";
		
		$this->view->message=$outString;
		error_log("Data/ExportAction() - FINISHED================================");	

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


}





