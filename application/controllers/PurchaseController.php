<?php

class PurchaseController extends Q_Controller_Base
{
	private $noCollectionFirstFour = array('9999' => 2, '8888' => 3, '9012' => 2, '9100' => 4);
	
	public function init()
	{
		
	}
	
	public function indexAction()
	{
		// action body
	}
	
	public function payAction()
	{
		$inData = $this->getRequest()->getPost('data');
		
		$newPurchaseRefId            = \Q\Utils::newGuid();
		$inData['purchase']['refId'] = $newPurchaseRefId;
		
		$purchaseObj                = new \Application_Model_Purchase();
		$purchase                   = $purchaseObj->generate();
		$purchaseObj->entity->refId = $newPurchaseRefId; //created above so it could be given to credit card processor, overrides default gen'd by entity, ok because it's new
		
		$accountObj = new \Application_Model_Account();
		$account    = $accountObj->getByRefId($inData['account']['refId']);
		$purchaseObj->addAccount($account);
		
		$orderEntityList     = array();
		$orderObjPersistList = array();
		
		for ($i = 0, $len = count($inData['orders']); $i < $len; $i++) {
			$element = $inData['orders'][$i];
			
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
			$purchaseObj->addOrder($order);
			
			$sortKey                   = $student->firstName . $day->seqNum . $student->refId;
			$orderEntityList[$sortKey] = $order; //collect orders for email
			$orderObjPersistList[] = $orderObj;
		}
		
		$errorList = \Application_Model_Purchase::validate($inData);
		$validOrder=count($errorList) == 0;
		
		//===========================
		
		$messages      = array();
		$processResult = array();
		
		$specialInstruction = substr($inData['cardData']['cardNumber'], 0, 4);
		
		if ($validOrder) {
			if ($this->requiresCollection($specialInstruction)) {
			
				$processResult = \Application_Model_Payment::process($inData);
				
				if ($processResult['approved']) {
					$emailSelectionCode = 1;
				} else {
					mail('tq@justkidding.com', 'Goodearth: Failed Credit Card', $processResult['response_reason_text'] . "\n\n");
					$errorList[] = array(
						'transaction',
						preg_replace('/^.*: /', '', $processResult['response_reason_text'])
					);
				}
			} else {
				
				$emailSelectionCode = $this->noCollectionFirstFour[$specialInstruction];
				if ($emailSelectionCode) {
					$processResult['deferredPaymentPreference'] = "DEFERRED by {$specialInstruction}";
				}
				
			}
			$this->addPaymentDataToPurchase($purchase, $inData, $processResult);
		}
		
		if (count($errorList) > 0) {
			$status   = -1;
			$messages = $errorList;
		} else {
			$status   = $emailSelectionCode ? $emailSelectionCode : 1;
			$messages = Q\Utils::flattenToList($processResult); //mainly for debugging ease, maybe should be removed later
			for ($i = 0, $len = count($orderObjPersistList); $i < $len; $i++) {
				$element = $orderObjPersistList[$i];
				$element->persist(Application_Model_Base::noFlush);
			}
			$purchaseObj->persist(Application_Model_Base::yesFlush); //I put "$this->emailReceipt($purchaseObj);" ahead of this line and it stopped persisting!?
			$this->emailReceipt($purchaseObj->entity->refId, $orderEntityList, $emailSelectionCode, $inData['account']['refId']);
		}
		
		$this->_helper->json(array(
			status => $status,
			messages => $messages,
			data => array(
				emailMessage => $this->emailMessage
			)
		));
		
	}
	
	private function requiresCollection($firstFour)
	{
		return !isset($this->noCollectionFirstFour[$firstFour]);
	}
	
	private function addPaymentDataToPurchase($purchase, $inData, $processResult)
	{
		
		$purchase->chargeTotal = $inData['cardData']['chargeTotal'];
		$purchase->cardName    = $inData['cardData']['cardName'];
		$purchase->street      = $inData['cardData']['street'];
		$purchase->city        = $inData['cardData']['city'];
		$purchase->state       = $inData['cardData']['state'];
		$purchase->zip         = $inData['cardData']['zip'];
		$purchase->phoneNumber = $inData['cardData']['phoneNumber'];
		$purchase->lastFour    = substr($inData['cardData']['cardNumber'], strlen($inData['cardData']['cardNumber']) - 4, 4);
		$purchase->firstFour   = substr($inData['cardData']['cardNumber'], 0, 4);
		
		$purchase->deferredPaymentPreference = $processResult['deferredPaymentPreference'];
		
		$purchase->fdProcessorResponseMessage = $processResult['approved'] ? 'APPROVED' : 'REJECTED';
		
		$purchase->fdProcessorReferenceNumber = $processResult['transaction_id'];
		$purchase->fdErrorMessage             = $processResult['response_reason_text'];
		$purchase->fdApprovalCode             = $processResult['authorization_code'];
		
		$date                        = new \DateTime(date("Y-m-d H:i:s"));
		$date                        = $date->format('Y-m-d H:i:s');
		$purchase->fdTransactionTime = $date;
		$purchase->fdOrderId         = $inData['purchase']['refId'];
		
		
		
	}
	
	//======================
	
	public function emailReceipt($purchaseRefId, $orderEntityList, $status, $accountRefId = 'n/a')
	{
		$auth = \Zend_Auth::getInstance();
		$user = $auth->getIdentity();
		
		// 		$purchaseObj=new Application_Model_Purchase();
		// 		$purchaseObj->getByRefId($purchaseRefId);
		
		$this->doctrineContainer = \Zend_Registry::get('doctrine');
		$this->entityManager     = $this->doctrineContainer->getEntityManager();
		$purchaseEntity          = $this->entityManager->find('GE\Entity\Purchase', $purchaseRefId);
		
		ksort($orderEntityList);
		
		$view = new Zend_View();
		$view->setScriptPath(APPLICATION_PATH . '/views/scripts/purchase');
		
		$view->user            = $user;
		$view->purchaseEntity  = $purchaseEntity;
		$view->orderEntityList = $orderEntityList;
		
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
		
		if ($status != 3) { //ie, payment code starts with '8888' for debugging
			$addressList   = $this->addSchoolAddresses($orderEntityList, $user);
			$addressList[] = array(
				'name' => 'Good Earth Organic School Lunch Program',
				'address' => 'school@genatural.com',
				'type' => 'accounting'
			);
		}
		
		$addressList[] = array(
			'name' => 'Website Programmer',
			'address' => 'tq@justkidding.com',
			'type' => 'accounting'
		);
		$addressList[] = array(
			'name' => $user->firstName . ' ' . $user->lastName,
			'address' => $user->emailAdr,
			'type' => 'customer'
		);
		
		switch ($status) {
			default:
				$emailMessage = $view->render('email-receipt.phtml');
				$emailSubject = "Good Earth Lunch Program Purchase Receipt";
				break;
			case '2':
				$emailMessage = $view->render('deferred-email-receipt.phtml');
				$emailSubject = "Good Earth Lunch Program Invoice";
				break;
			case '3':
				$emailMessage = $view->render('deferred-email-receipt.phtml');
				$emailSubject = "Good Earth Lunch Program Invoice";
				break;
			case '4':
				$emailMessage = $view->render('fr-email-receipt.phtml');
				$emailSubject = "Good Earth Lunch Program Notification";
				break;
		}
		$this->emailMessage = $emailMessage;
		
		for ($i = 0, $len = count($addressList); $i < $len; $i++) {
			$element = $addressList[$i];
			$mail    = new Zend_Mail();
			$mail->setSubject($emailSubject);
			$mail->setBodyHtml($emailMessage);
			
			$mail->addTo($element['address'], $element['name']);
			
			$status = $mail->send($tr);
			
		}
		
		Zend_Mail::clearDefaultFrom();
		Zend_Mail::clearDefaultReplyTo();
		return true;
	}
	
	private function addSchoolAddresses($orderList, $user)
	{
		$rawAddressList = array();
		$addressList    = array();
		
		$list        = $orderList;
		$addresslist = array();
		
		foreach ($list as $label => $element) {
			
			if ($element->student->school->emailAdr) {
				$rawAddressList[$element->student->school->emailAdr] = $element->student->school->name;
			}
		}
		
		foreach ($rawAddressList as $address => $name) {
			
			$addressList[] = array(
				'name' => $name . ' School Lunch Volunteer',
				'address' => $address,
				'type' => 'school'
			);
		}
		return $addressList;
		
	}
	
}
