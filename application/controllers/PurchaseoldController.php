<?php

class PurchaseoldController extends  Q_Controller_Base
{

    public function init()
    {
        
    }

    public function indexAction()
    {
        // action body
    }

    public function payAction()
    {
		$inData=$this->getRequest()->getPost('data');
		$messages=array();
		$processResult=array();

		$this->emailLogList="process start == ";

		$specialInstruction=substr($inData['cardData']['cardNumber'], 0, 4);

		$newPurchaseRefId=\Q\Utils::newGuid();
		$inData['purchase']['refId']=$newPurchaseRefId;


error_log("PURCHASE STARTED:  account: {$inData['account']['refId']} purchase: $newPurchaseRefId");

		$errorList=\Application_Model_Purchase::validate($inData);

		if (count($errorList)==0){
			if (
				$specialInstruction!=='9999'
				&& $specialInstruction!=='8888'
				&& $specialInstruction!=='9012'
				&& $specialInstruction!=='9100' //not implemented: definition, F&R, *only* sends mail to user, not schools, sherry or anyone
				&& $specialInstruction!=='9101' //not implemented: definition, F&R, *only* sends mail to sherry, not schools, user or anyone
				&& $specialInstruction!=='9022'
			){
				
				$processResult=\Application_Model_Payment::process($inData);


// 		'approved'=>($result->response_code==1)?true:false,
// 		'explanation'=>$result->response_reason_text,
// 		'transactionId'=>$result->transaction_id


				if (!$processResult['approved']){
				
error_log("PURCHASE PAYMENT COMPLETE:  account: {$inData['account']['refId']} purchase: $newPurchaseRefId FAILED {$processResult['response_reason_text']}");
				
$tmp=array();
$tmp['inData']=$inData;
$tmp['inData']['cardData']['cardNumber']=$specialInstruction;
$tmp['processResult']=$processResult;
$resultString=$processResult['response_reason_text']."\n\n";
$resultString.=\Q\Utils::dumpCliString($tmp, "debug info");
mail('tq@justkidding.com', 'Goodearth: Failed Credit Card', $resultString);


					$status=-1;
					if ($processResult['DETAIL']){
						$errorList[]=array('transaction', preg_replace('/^.*: /', '', $processResult['response_reason_text']));
					}
					else{
						$errorList[]=array('transaction', preg_replace('/^.*: /', '', $processResult['response_reason_text']));
					}
				}
				else{
error_log("PURCHASE PAYMENT COMPLETE:  account: {$inData['account']['refId']} purchase: $newPurchaseRefId SUCCESS {$processResult['response_reason_text']}");
					$status=1;
				}
			}
			else{	

$tmp=array();
$tmp['inData']=$inData;
$tmp['inData']['cardData']['cardNumber']=$specialInstruction;
$tmp['processResult']=$processResult;
$resultString=$processResult['response_reason_text']."\n\n";
$resultString.=\Q\Utils::dumpCliString($tmp, "debug info");

				switch ($specialInstruction){
					case '9999':
						$processResult['deferredPaymentPreference']='DEFERRED by 9999';
						$status=2;
// 						$emailMessage=$view->render('deferred-email-receipt.phtml');
// 						$emailSubject="Good Earth Lunch Program Invoice";
						break;
					case '9012':
						$processResult['deferredPaymentPreference']='DEFERRED by 9012';
						$status=2;
// 						$emailMessage=$view->render('deferred-email-receipt.phtml');
// 						$emailSubject="Good Earth Lunch Program Invoice";
						break;
					case '8888':
						$processResult['deferredPaymentPreference']='DEFERRED by 8888';
						$status=3;
// 						$emailMessage=$view->render('deferred-email-receipt.phtml');
// 						$emailSubject="Good Earth Lunch Program Invoice";
						break;
					case '9022':
						$processResult['deferredPaymentPreference']='DEFERRED by 9022';
						$status=4;
// 						$emailMessage=$view->render('fr-email-receipt.phtml');
// 						$emailSubject="Good Earth Lunch Program Notification";
						break;
				}
				

error_log("PURCHASE PAYMENT COMPLETE:  account: {$inData['account']['refId']} purchase: $newPurchaseRefId FREE {$processResult['deferredPaymentPreference']}");
			}
		}
		

		if (count($errorList)>0){

$collapsedErrors=join(', ', $errorList);

error_log("PURCHASE COMPLETED WITH ERRORS:  account: {$inData['account']['refId']} purchase: $newPurchaseRefId $collapsedErrors");
			$this->_helper->json(array(
				status=>-1,
				messages=>$errorList,
				data=>array()
			));
		}
		else{
				$status=$status?$status:1;

				$messages=Q\Utils::flattenToList($processResult); //mainly for debugging ease, maybe should be removed later


error_log("PURCHASE DATABASE START: account: {$inData['account']['refId']} purchase: $newPurchaseRefId");



				$purchaseObj=new \Application_Model_Purchase();
				$purchase=$purchaseObj->generate();
				$purchaseObj->entity->refId=$newPurchaseRefId; //created above so it could be given to credit card processor, overrides default gen'd by entity, ok because it's new

				$purchase->chargeTotal=$inData['cardData']['chargeTotal'];
				$purchase->cardName=$inData['cardData']['cardName'];
				$purchase->street=$inData['cardData']['street'];
				$purchase->city=$inData['cardData']['city'];
				$purchase->state=$inData['cardData']['state'];
				$purchase->zip=$inData['cardData']['zip'];
				$purchase->phoneNumber=$inData['cardData']['phoneNumber'];
				$purchase->lastFour=substr($inData['cardData']['cardNumber'], strlen($inData['cardData']['cardNumber'])-4, 4);
				$purchase->firstFour=substr($inData['cardData']['cardNumber'], 0, 4);

				$purchase->deferredPaymentPreference=$processResult['deferredPaymentPreference'];

				$msg=$processResult['approved']?'APPROVED':'REJECTED';
				$purchase->fdProcessorResponseMessage=$msg;
				
				$purchase->fdProcessorReferenceNumber=$processResult['transaction_id'];
				$purchase->fdErrorMessage=$processResult['response_reason_text'];
				$purchase->fdApprovalCode=$processResult['authorization_code'];
				
				$date=new \DateTime(date("Y-m-d H:i:s"));
				$date=$date->format('Y-m-d H:i:s');
				$purchase->fdTransactionTime=$date;
				$purchase->fdOrderId=$inData['purchase']['refId'];

				$accountObj=new \Application_Model_Account();
				$account=$accountObj->getByRefId($inData['account']['refId']);
				$purchaseObj->addAccount($account);
$debugElementArray=array();
				$list=$inData['orders'];
				$orderEntityList=array();
				for ($i=0, $len=count($list); $i<$len; $i++){
					$element=$list[$i];

					$studentObj=new \Application_Model_Student();
					$student=$studentObj->getByRefId($element['student']['refId']);

					$offeringObj=new \Application_Model_Offering();
					$offering=$offeringObj->getByRefId($element['offer']['refId']);

					$dayObj=new \Application_Model_Day();
					$day=$dayObj->getByRefId($element['day']['refId']);

					$orderObj=new \Application_Model_Order();
					$order=$orderObj->generate();
					$order->currPeriodFull=$offering->perYearFull;
					$order->student=$student;
					$order->offering=$offering;
					$order->day=$day;
					$orderObj->persist(Application_Model_Base::noFlush);
					$purchaseObj->addOrder($order);

					$sortKey=$student->firstName.$day->seqNum.$student->refId;
					$orderEntityList[$sortKey]=$order; //collect orders for email
$debugElementArray[]=array(
'studentRefId'=>$element['student']['refId'],
'offeringRefId'=>$element['offer']['refId'],
'dayRefId'=>$element['day']['refId'],
'accountRefId'=>$inData['account']['refId']
);

				}


				$purchaseObj->persist(Application_Model_Base::yesFlush); //I put "$this->emailReceipt($purchaseObj);" ahead of this line and it stopped persisting!?

error_log("PURCHASE DATABASE COMPLETE:  account: {$inData['account']['refId']} purchase: $newPurchaseRefId");

error_log("PURCHASE EMAIL START:  account: {$inData['account']['refId']} purchase: $newPurchaseRefId");
				$this->emailReceipt($purchaseObj->entity->refId, $orderEntityList, $status, $inData['account']['refId']);

error_log("PURCHASE EMAIL COMPLETE:  account: {$inData['account']['refId']} purchase: $newPurchaseRefId");

$purchaseObj=array(
	'chargeTotal'=>$inData['cardData']['chargeTotal'],
	'cardName'=>$inData['cardData']['cardName']
);


error_log("PURCHASE PROCESS COMPLETE: account: {$inData['account']['refId']} purchase: $newPurchaseRefId");
error_log("======================================================================");

			$this->_helper->json(array(
				status=>$status,
				messages=>$messages,
				data=>array(emailMessage=>$this->emailMessage)
			));


    }
    
    }

    public function emailReceipt($purchaseRefId, $orderEntityList, $status, $accountRefId='n/a')
    {
        $auth = \Zend_Auth::getInstance();
		$user=$auth->getIdentity();

// 		$purchaseObj=new Application_Model_Purchase();
// 		$purchaseObj->getByRefId($purchaseRefId);


		$this->doctrineContainer=\Zend_Registry::get('doctrine');
		$this->entityManager=$this->doctrineContainer->getEntityManager();
		$purchaseEntity=$this->entityManager->find('GE\Entity\Purchase', $purchaseRefId);


		ksort($orderEntityList);

		$view = new Zend_View();
		$view->setScriptPath(APPLICATION_PATH.'/views/scripts/purchase');

		$view->user=$user;
		$view->purchaseEntity=$purchaseEntity;
		$view->orderEntityList=$orderEntityList;

    	$emailSender=Zend_Registry::get('emailSender');
    	
    	if (!$emailSender){
			$tr=new Zend_Mail_Transport_Sendmail();
		}
		else{
			$tr=new Zend_Mail_Transport_Smtp($emailSender['hostName'], array(
				'username'=>$emailSender['authSet']['username'],
				'password'=>$emailSender['authSet']['password'],
				'port'=>$emailSender['authSet']['port'],
				'ssl'=>$emailSender['authSet']['ssl'],
				'auth'=>$emailSender['authSet']['auth']
			));

		}
		
		Zend_Mail::setDefaultTransport($tr);
		Zend_Mail::setDefaultFrom($emailSender['fromAddress'], $emailSender['fromName']);
		Zend_Mail::setDefaultReplyTo($emailSender['fromAddress'], $emailSender['fromName']);

		if ($status!=3){ //ie, payment code starts with '8888' for debugging
			$addressList=$this->addSchoolAddresses($orderEntityList, $user);
			$addressList[]=array('name'=>'Good Earth Organic School Lunch Program', 'address'=>'school@genatural.com', 'type'=>'accounting');
		}

		$addressList[]=array('name'=>'Website Programmer', 'address'=>'tq@justkidding.com', 'type'=>'accounting');
		$addressList[]=array('name'=>$user->firstName.' '.$user->lastName, 'address'=>$user->emailAdr, 'type'=>'customer');

		switch($status){
			default:
				$emailMessage=$view->render('email-receipt.phtml');
				$emailSubject="Good Earth Lunch Program Purchase Receipt";
				break;
			case '2':
				$emailMessage=$view->render('deferred-email-receipt.phtml');
				$emailSubject="Good Earth Lunch Program Invoice";
				break;
			case '3':
				$emailMessage=$view->render('deferred-email-receipt.phtml');
				$emailSubject="Good Earth Lunch Program Invoice";
				break;
			case '4':
				$emailMessage=$view->render('fr-email-receipt.phtml');
				$emailSubject="Good Earth Lunch Program Notification";
				break;
		}
$this->emailMessage=$emailMessage;

		for ($i=0, $len=count($addressList); $i<$len; $i++){
			$element=$addressList[$i];
			$mail = new Zend_Mail();
			$mail->setSubject($emailSubject);
			$mail->setBodyHtml($emailMessage);

			$mail->addTo($element['address'], $element['name']);
			$this->emailLogList.="({$element['address']} {$element['name']}) ";

error_log("PURCHASE EMAIL SENDING: purchase: $purchaseRefId, accountRefId:$accountRefId, user: {$user->userName}, dest email: {$element['address']}, server: {$emailSender['hostName']}, ");
			$status=$mail->send($tr);
error_log("PURCHASE EMAIL SENT: purchase: $purchaseRefId, accountRefId:$accountRefId, user: {$user->userName}, dest email: {$element['address']}, server: {$emailSender['hostName']}, ");

		}

error_log("PURCHASE EMAIL COMPLETE: purchase: $purchaseRefId, accountRefId:$accountRefId, user: {$user->userName}");

		Zend_Mail::clearDefaultFrom();
		Zend_Mail::clearDefaultReplyTo();
		return true;
	}

	private function addSchoolAddresses($orderList, $user){
		$rawAddressList=array();
		$addressList=array();

			$list=$orderList;
			$addresslist=array();

		foreach ($list as $label=>$element){

				if ($element->student->school->emailAdr){
					$rawAddressList[$element->student->school->emailAdr]=$element->student->school->name;
				}
			}

			foreach($rawAddressList as $address=>$name){

				$addressList[]=array(
					'name'=>$name.' School Lunch Volunteer',
					'address'=>$address,
					'type'=>'school'
				);
			}
		return $addressList;


	}


}
