<?php

class PurchaseController extends Q_Controller_Base
{
	private function processingParameters($selector)
	{
		$noCollectionFirstFour = array(
			'9999' => array(
				'templateName' => "deferred-email-receipt.phtml",
				'code' => 2,
				'emailSubject' => "Good Earth Lunch Program Invoice",
				'processingRequired' => false
			),
			'9012' => array(
				'templateName' => "deferred-email-receipt.phtml",
				'code' => 2,
				'emailSubject' => "Good Earth Lunch Program Invoice",
				'processingRequired' => false
			),
			'8888' => array(
				'tqOnly' => true,
				'templateName' => "deferred-email-receipt.phtml",
				'code' => 3,
				'emailSubject' => "Good Earth Lunch Program Invoice",
				'processingRequired' => false
			),
			'9100' => array(
				'templateName' => "fr-email-receipt.phtml",
				'code' => 4,
				'emailSubject' => "Good Earth Lunch Program Notification",
				'processingRequired' => false
			)
		);

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
	
	/*
	NEXT:
		it saves the three purchases successfully but they 
		each get the full price of the deal. I need
		to calculate the sum of the orders for each deal.
		Probably make the $merchantAccountOrders return a sum
		as well as a list of orders and put that into the purchase 
		when it is created.
		
		Once done, it's on to payment.
	
	
	
	
	*/

	private function copyInDataToPurchase($purchaseGenerated, $inData){

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

		$purchase->deferredPaymentPreference = $paymentProcessResult['deferredPaymentPreference'];

		$purchase->fdProcessorResponseMessage = $paymentProcessResult['approved'] ? 'APPROVED' : 'REJECTED';

		$purchase->fdProcessorReferenceNumber = $paymentProcessResult['transaction_id'];
		$purchase->fdErrorMessage             = $paymentProcessResult['response_reason_text'];
		$purchase->fdApprovalCode             = $paymentProcessResult['authorization_code'];

		$date                        = new \DateTime(date("Y-m-d H:i:s"));
		$date                        = $date->format('Y-m-d H:i:s');
		$purchase->fdTransactionTime = $date;
		$purchase->fdOrderId         = $inData['purchase']['refId'];
	}

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

	private function sendCustomerEmail($purchaseRefId, $processControl)
	{
		$auth = \Zend_Auth::getInstance();
		$user = $auth->getIdentity();

		$doctrineContainer = \Zend_Registry::get('doctrine');
		$entityManager     = $doctrineContainer->getEntityManager();
		$entityManager->clear();
		$purchaseEntity    = $entityManager->find('GE\Entity\Purchase', $purchaseRefId); 

		$orderEntityList = $this->getOrderList($purchaseEntity);

		$view = new Zend_View();
		$view->setScriptPath(APPLICATION_PATH . '/views/scripts/purchase');
		$view->user            = $user;
		$view->purchaseEntity  = $purchaseEntity;
		$view->orderEntityList = $orderEntityList;

		$renderedMessage = $view->render($processControl['templateName']);
		$emailSubject    = $processControl['emailSubject'];
		$addressList     = $this->setUpDestinationAddresses($processControl, $orderEntityList, $user);
		$emailSendStatus = $this->transmitEmail($addressList, $emailSubject, $renderedMessage);

		return array(
			'addressList' => $addressList,
			processControl => $processControl,
			emailSendStatus => $emailSendStatus
		);
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
		$this->initEmailSender(); //gets stuff from registry, etc

		for ($i = 0, $len = count($addressList); $i < $len; $i++) {
			$recipient = $addressList[$i];
			$mail      = new Zend_Mail();
			$mail->setSubject($emailSubject);
			$mail->setBodyHtml($emailMessage);
			$mail->addTo($recipient['address'], $recipient['name']);
			$emailSendStatus = $mail->send($tr);

			Zend_Mail::clearDefaultFrom();
			Zend_Mail::clearDefaultReplyTo();

			return $emailSendStatus;
		}
	}
	
	private function organizeIntoPaymentGroups($orders){
		$merchantAccountOrders = array();

		for ($i = 0, $len = count($orders); $i < $len; $i++) {
			$order             = $this->constructOrderObj($orders[$i]);
			
			$merchantAccountId = $order->student->school->merchantAccountId;
			$merchantAccountId = $merchantAccountId ? $merchantAccountId : 'default';

			if (!$merchantAccountOrders[$merchantAccountId]) {
				$merchantAccountOrders[$merchantAccountId] = array();
			}
			$merchantAccountOrders[$merchantAccountId][] = $order;
		}
			
		return $merchantAccountOrders;
	}

	private function assemblePurchases($merchantAccountOrders, $account, $inData){
		$purchaseModelList=array();
		foreach ($merchantAccountOrders as $merchantAccountId => $orderList) {
				$purchaseModel                = new \Application_Model_Purchase();
				$purchaseGenerated                   = $purchaseModel->generate();
				
				$purchaseModel->entity->refId = \Q\Utils::newGuid();
				$purchaseModel->entity->merchantAccountId=$merchantAccountId;
				$this->copyInDataToPurchase($purchaseGenerated, $inData); //for some reason this has to be here, not later in the text
				$purchaseModel->addAccount($account);
				
				for ($i = 0, $len = count($orderList); $i < $len; $i++) {
					$order = $orderList[$i];
					$chargeAmount+=$order->offering->price;
					$purchaseModel->addOrder($order);
				}
				
				$purchaseModelList[] = $purchaseModel;
	
			}
			return $purchaseModelList;
	}
	
	public function payAction()
	{
		$inData = $this->getRequest()->getPost('data');
		
		$errorList  = \Application_Model_Purchase::validate($inData);

		if (count($errorList) == 0)  {

		$merchantAccountOrders = $this->organizeIntoPaymentGroups($inData['orders']);

		$inData['purchase']['refId'] = \Q\Utils::newGuid();

		$purchaseModelList = $this->assemblePurchases($merchantAccountOrders, $account, $inData);

		$specialInstruction = substr($inData['cardData']['cardNumber'], 0, 4);
		$processControl     = $this->processingParameters($specialInstruction);
			if ($processControl['processingRequired']) {

				$paymentProcessResult = \Application_Model_Payment::process($purchaseModelList, $inData);

				if (!$paymentProcessResult['approved']) {
					$errorList[] = array(
						'transaction',
						preg_replace('/^.*: /', '', $processResult['response_reason_text'])
					);
					error_log(preg_replace('/^.*: /', '', $paymentProcessResult['response_reason_text']));
				}
			} else {
				$paymentProcessResult['deferredPaymentPreference'] = "DEFERRED by {$specialInstruction}";
			}
		}

		if (count($errorList) > 0) {
			$status       = -1;
			$messages     = $errorList;
			$emailMessage = "no email message sent";
		} else {

			for ($i=0, $len=count($purchaseModelList); $i<$len; $i++){
				$purchaseModel=$purchaseModelList[$i];
				$this->addPaymentResultToPurchase($purchaseGenerated, $inData, $paymentProcessResult);
				$purchaseModel->persist(Application_Model_Base::yesFlush);
			}
			
			$emailMessage = $this->sendCustomerEmail($purchaseObj->entity->refId, $processControl);
	
			$status       = $processControl['code'] ? $processControl['code'] : 1;
			$messages     = Q\Utils::flattenToList($paymentProcessResult); //mainly for debugging ease, maybe should be removed later
		}

		$this->_helper->json(array(
			status => $status,
			messages => $messages,
			data => array(
				emailMessage => $emailMessage
			)
		));

	}

}
