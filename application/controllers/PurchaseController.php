<?php

class PurchaseController extends Q_Controller_Base
{
	
	// invoice email functions =======================
	
	private function getOrderList($purchaseEntity)
	{
		$orderEntityList = array();
		for ($i = 0, $len = count($purchaseEntity->purchaseOrderNodes); $i < $len; $i++) {
			$element                   = $purchaseEntity->purchaseOrderNodes[$i]->order;
			$sortKey                   = $element->student->firstName . $element->day->seqNum . $element->student->refId;
			$orderEntityList[$sortKey] = $element;
		}
		ksort($orderEntityList);
		return $orderEntityList;
	}
	
	private function addSchoolAddresses($orderList)
	{
		$dupeSuppressList = array();
		$addressList      = array();
		$addresslist      = array();
		foreach ($orderList as $label => $element) {
			$address = $element->student->school->emailAdr;
			$name    = $element->student->school->name;
			if ($address) {
				$dupeSuppressList[$address] = $name; //suppress duplicates if there are two kids for same school
			}
		}
		foreach ($dupeSuppressList as $address => $name) {
			$addressList[] = array(
				'name' => $name . ' School Lunch Volunteer',
				'address' => $address,
				'type' => 'school'
			);
		}
		return $addressList;
	}
	
	private function setUpDestinationAddresses($processControl, $orderEntityList, $user)
	{
		
		if (!$processControl['tqOnly']) { //ie, payment code starts with '8888' for debugging
			$addressList   = $this->addSchoolAddresses($orderEntityList);
			$addressList[] = array(
				'name' => 'Good Earth Organic School Lunch Program',
				'address' => 'school@genatural.com',
				'type' => 'accounting'
			);
			
			if (!$user->emailAdr){
				error_log("setUpDestinationAddresses() says user->emailAdr is empty for user refId {$user->refId} [purchaseController.php]");
			}
			
			$addressList[] = array(
				'name' => $user->firstName . ' ' . $user->lastName,
				'address' => $user->emailAdr,
				'type' => 'customer'
			);
		}
		
		$addressList[] = array(
			'name' => 'Website Programmer',
			'address' => 'tq@justkidding.com',
			'type' => 'accounting'
		);
		
		return $addressList;
	}
	
	private function initEmailSender()
	{
		$emailSender = Zend_Registry::get('emailSender');
		if (!$emailSender) {
			$tr = new Zend_Mail_Transport_Sendmail();
		} else {
			$tr = new Zend_Mail_Transport_Smtp($emailSender['hostName'], array(
				'username' => $emailSender['authSet']['username'],
				'password' => $emailSender['authSet']['password'],
				'port' => $emailSender['authSet']['port'],
				'ssl' => $emailSender['authSet']['ssl'],
				'auth' => $emailSender['authSet']['auth']
			));
		}
		Zend_Mail::setDefaultTransport($tr);
		Zend_Mail::setDefaultFrom($emailSender['fromAddress'], $emailSender['fromName']);
		Zend_Mail::setDefaultReplyTo($emailSender['fromAddress'], $emailSender['fromName']);
		return;
	}
	
	private function transmitEmail($addressList, $emailSubject, $emailMessage)
	{
		$outList=array();
		$this->initEmailSender(); //gets stuff from registry, etc
		for ($i = 0, $len = count($addressList); $i < $len; $i++) {
		
// 			error_log("DEBUG: FORCE NO EMAIL SENDING");
// 			continue;
		
			$recipient = $addressList[$i];
			$mail      = new Zend_Mail();
			$mail->setSubject($emailSubject);
			$mail->setBodyHtml($emailMessage);
			$mail->addTo($recipient['address'], $recipient['name']);
			error_log("attempting email: {$recipient['address']} for ({$recipient['name']} [PurchaseController.php]");	
			if (!$recipient['address']){
				error_log("email address is missing [PurchaseController.php]");
			}
			else{
				$outList[] = $mail->send($tr);
				error_log("sent email: {$recipient['address']} [PurchaseController.php]");
			}
		}
			
		return $outList;
	}
	
	private function sendCustomerEmail($purchaseRefId, $processControl, $purchaseModelList)
	{
		$auth = \Zend_Auth::getInstance();
		$user = $auth->getIdentity();
		
		error_log("sendCustomerEmail() says, starting email process for {$user->refId},  {$user->emailAdr} [purchaseController.php");
		
		$doctrineContainer = \Zend_Registry::get('doctrine');
		$entityManager     = $doctrineContainer->getEntityManager();
		$entityManager->clear();
		
		$renderedMessage   = '';
		$purchasesViewData = array();
		
		for ($i = 0, $len = count($purchaseModelList); $i < $len; $i++) {
			$purchaseRefId = $purchaseModelList[$i]->entity->refId;
			
			$purchaseEntity = $entityManager->find('GE\Entity\Purchase', $purchaseRefId);
			
			$orderEntityList     = $this->getOrderList($purchaseEntity);
			$purchasesViewData[] = array(
				purchaseEntity => $purchaseEntity,
				orderEntityList => $orderEntityList
			);
			
		}
		
		
		$view = new Zend_View();
		$view->setScriptPath(APPLICATION_PATH . '/views/scripts/purchase');
		$view->user              = $user;
		$view->purchasesViewData = $purchasesViewData;
		
		$renderedMessage .= $view->render($processControl['templateName']);
		
		$emailSubject    = $processControl['emailSubject'];
		$addressList     = $this->setUpDestinationAddresses($processControl, $orderEntityList, $user);
		$emailSendStatus = $this->transmitEmail($addressList, $emailSubject, $renderedMessage);
		
		return array(
			'addressList' => $addressList,
			processControl => $processControl,
			emailSendStatus => $emailSendStatus,
			renderedMessage => $renderedMessage
		);
	}
	
	// payment processing functions =======================
	
	private function processingParameters($cardData)
	{
		
		$noCollectionFirstFour = array(
			'9999' => array(
				'tqOnly' => true,
				'description'=>"TQ debugging deferred template, only sent to him (9999)",
				'templateName' => "deferred-email-receipt.phtml",
				'code' => 3,
				'emailSubject' => "Good Earth Lunch Program Receipt",
				'processingRequired' => false
			),
			'paybycheck' => array(
				'description'=>"deferred payment (9012)",
				'templateName' => "deferred-email-receipt.phtml",
				'code' => 2,
				'emailSubject' => "Good Earth Lunch Program Invoice",
				'processingRequired' => false
			),
			'9012' => array(
				'description'=>"deferred payment (9012)",
				'templateName' => "deferred-email-receipt.phtml",
				'code' => 2,
				'emailSubject' => "Good Earth Lunch Program Invoice",
				'processingRequired' => false
			),
			'8888' => array(
				'tqOnly' => true,
				'description'=>"TQ debugging normal template, only sent to him (8888)",
				'templateName' => "email-receipt.phtml",
				'code' => 3,
				'emailSubject' => "Good Earth Lunch Program Invoice",
				'processingRequired' => false
			),
			'9100' => array(
				'description'=>"free and reduced (9100)",
				'templateName' => "fr-email-receipt.phtml",
				'code' => 4,
				'emailSubject' => "Good Earth Lunch Program Notification",
				'processingRequired' => false
			)
		);
		$selector=strtolower($cardData); //try long version first
		$selector=preg_replace('/[^\S]/', '', $selector);

		if (isset($noCollectionFirstFour[$selector])) {
			return $noCollectionFirstFour[$selector];
		}
		
		$selector = substr($selector, 0, 4);
		if (isset($noCollectionFirstFour[$selector])) {
			return $noCollectionFirstFour[$selector];
		}
		
		return array(
			'templateName' => "email-receipt.phtml",
			'code' => '1',
			'emailSubject' => "Good Earth Lunch Program Purchase Receipt",
			'processingRequired' => true
		);
	}
	
	private function constructOrderObj($element)
	{
		
		$studentObj = new \Application_Model_Student();
		$student    = $studentObj->getByRefId($element['student']['refId']);
		
		$offeringObj = new \Application_Model_Offering();
		$offering    = $offeringObj->getByRefId($element['offer']['refId']);
		
		$dayObj = new \Application_Model_Day();
		$day    = $dayObj->getByRefId($element['day']['refId']);
		
		$orderObj              = new \Application_Model_Order();
		$order                 = $orderObj->generate();
		$order->currPeriodFull = $offering->perYearFull;
		$order->student        = $student;
		$order->offering       = $offering;
		$order->day            = $day;
		
		$sortKey = $student->firstName . $day->seqNum . $student->refId;
		
		return $order;
	}
	
	private function copyInDataToPurchase($purchaseGenerated, $inData)
	{
		
		$purchaseGenerated->chargeTotal = 0;
		$purchaseGenerated->cardName    = $inData['cardData']['cardName'];
		$purchaseGenerated->street      = $inData['cardData']['street'];
		$purchaseGenerated->city        = $inData['cardData']['city'];
		$purchaseGenerated->state       = $inData['cardData']['state'];
		$purchaseGenerated->zip         = $inData['cardData']['zip'];
		$purchaseGenerated->phoneNumber = $inData['cardData']['phoneNumber'];
		$purchaseGenerated->lastFour    = substr($inData['cardData']['cardNumber'], strlen($inData['cardData']['cardNumber']) - 4, 4);
		$purchaseGenerated->firstFour   = substr($inData['cardData']['cardNumber'], 0, 4);
		
	}
	
	private function addPaymentResultToPurchase($purchase, $inData, $paymentProcessResult)
	{
		
		$purchase->entity->deferredPaymentPreference = $paymentProcessResult['deferredPaymentPreference'];
		
		$purchase->entity->fdProcessorResponseMessage = $paymentProcessResult['approved'] ? 'APPROVED' : 'REJECTED';
		
		$purchase->entity->fdProcessorReferenceNumber = $paymentProcessResult['transaction_id'];
		$purchase->entity->fdErrorMessage             = $paymentProcessResult['response_reason_text'];
		$purchase->entity->fdApprovalCode             = $paymentProcessResult['authorization_code'];
		
		$date                        = new \DateTime(date("Y-m-d H:i:s"));
		$date                        = $date->format('Y-m-d H:i:s');
		$purchase->entity->fdTransactionTime = $date;
		$purchase->entity->fdOrderId         = $inData['purchase']['refId'];
	}
	
	private function organizeIntoPaymentGroups($orders)
	{
		$merchantAccountOrders = array();
		
		for ($i = 0, $len = count($orders); $i < $len; $i++) {
			$order = $this->constructOrderObj($orders[$i]);
			
			$merchantAccountId = $order->student->school->merchantAccountId;
			$merchantAccountId = $merchantAccountId ? $merchantAccountId : 'default';
			
			if (!$merchantAccountOrders[$merchantAccountId]) {
				$merchantAccountOrders[$merchantAccountId] = array();
			}
			$merchantAccountOrders[$merchantAccountId][] = $order;
		}
		
		return $merchantAccountOrders;
	}
	
	private function assemblePurchases($merchantAccountOrders, $account, $inData)
	{
		$purchaseModelList = array();
		foreach ($merchantAccountOrders as $merchantAccountId => $orderList) {
			$purchaseModel     = new \Application_Model_Purchase();
			$purchaseGenerated = $purchaseModel->generate();
			
			$purchaseModel->entity->refId             = \Q\Utils::newGuid();
			$purchaseModel->entity->merchantAccountId = $merchantAccountId;
			$this->copyInDataToPurchase($purchaseGenerated, $inData); //for some reason this has to be here, not later in the text
			$purchaseModel->addAccount($account);
			
			for ($i = 0, $len = count($orderList); $i < $len; $i++) {
				$order = $orderList[$i];
				$chargeAmount += $order->offering->price;
				$purchaseModel->addOrder($order);
				
			}
			$purchaseModelList[] = $purchaseModel;
			
		}
		return $purchaseModelList;
	}
	private function summarizeProcessResults($paymentProcessResult)
	{
		$outArray = array();
		for ($i = 0, $len = count($paymentProcessResult); $i < $len; $i++) {
			$result   = $paymentProcessResult[$i]['result'];
			$outArray = $result;
			if (!$result['approved']) {
				return $result;
			}
		}
		return $outArray;
	}
	public function payAction()
	{
		$inData = $this->getRequest()->getPost('data');
		error_log("START payment process {$inData['account']['refId']} [purchaseController.php]");
		
		$errorList = \Application_Model_Purchase::validate($inData);
		
		if (count($errorList) == 0) {
		
			$accountObj=new \Application_Model_Account();
			$account=$accountObj->getByRefId($inData['account']['refId']);
			
			$merchantAccountOrders = $this->organizeIntoPaymentGroups($inData['orders']);
			
			$inData['purchase']['refId'] = \Q\Utils::newGuid();
			
			$purchaseModelList = $this->assemblePurchases($merchantAccountOrders, $account, $inData);
			
			$processControl     = $this->processingParameters($inData['cardData']['cardNumber']);
			if ($processControl['processingRequired']) {
			
				error_log("initiating Application_Model_Payment::process() for {$inData['account']['refId']} [purchaseController.php]");
				$paymentProcessResult = \Application_Model_Payment::process($purchaseModelList, $inData);
				error_log("returned from Application_Model_Payment::process() for {$inData['account']['refId']} [purchaseController.php]");
				
				$summaryResult = $this->summarizeProcessResults($paymentProcessResult);
				
				if (!$summaryResult['approved']) {
					$errorList[] = array(
						'response_reason_text',
						preg_replace('/^.*: /', '', $summaryResult['response_reason_text'])
					);
				}
			} else {
				$paymentProcessResult['deferredPaymentPreference'] = "DEFERRED by {$processControl['description']}";
				error_log("DEFERRED by {$processControl['description']} [PurchaseController.php]");
			}
		}
		
		if (count($errorList) > 0) {
			$status       = -1;
			$messages     = $errorList;
			$emailMessage = "no email message sent";
		} else {
			
			for ($i = 0, $len = count($purchaseModelList); $i < $len; $i++) {
				$purchaseModel = $purchaseModelList[$i];
				$this->addPaymentResultToPurchase($purchaseModel, $inData, $paymentProcessResult[$i]['result']);
				error_log("init purchaseModel->persist(Application_Model_Base::yesFlush) for {$inData['account']['refId']} [purchaseController.php]");
				$purchaseModel->persist(Application_Model_Base::yesFlush);
				error_log("returned from purchaseModel->persist(Application_Model_Base::yesFlush) for {$inData['account']['refId']} [purchaseController.php]");
			}
			
			$emailMessage = $this->sendCustomerEmail($purchaseObj->entity->refId, $processControl, $purchaseModelList);
			
			$status   = $processControl['code'] ? $processControl['code'] : 1;
			$messages = 'Q\Utils::flattenToList($paymentProcessResult)??'; //; //mainly for debugging ease, maybe should be removed later
		}
		error_log("END payment process {$inData['account']['refId']} [purchaseController.php]");
		$this->_helper->json(array(
			status => $status,
			messages => $messages,
			data => array(
				emailMessage => $emailMessage
			)
		));
		
	}
	
}
