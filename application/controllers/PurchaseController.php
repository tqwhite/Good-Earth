<?php

class PurchaseController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
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

		$specialInstruction=substr($inData['cardData']['cardNumber'], 0, 4);

		$errorList=\Application_Model_Purchase::validate($inData);

		if (count($errorList)==0){
			if ($specialInstruction!=='9999' && $specialInstruction!=='8888' && $specialInstruction!=='9012'){
				$processResult=\Application_Model_Payment::process($inData);
				if ($processResult['FDGGWSAPI:TRANSACTIONRESULT']!='APPROVED'){
					$status=-1;
					if ($processResult['DETAIL']){
						$errorList[]=array('transaction', preg_replace('/^.*: /', '', $processResult['DETAIL']));
					}
					else{
						$errorList[]=array('transaction', preg_replace('/^.*: /', '', $processResult['FDGGWSAPI:ERRORMESSAGE']));
					}
				}
				else{
					$status=1;
				}
			}
			else{
				switch ($specialInstruction){
					case '9999':
						$processResult['deferredPaymentPreference']='DEFERRED by 9999';
						$status=2;
						break;
					case '9012':
						$processResult['deferredPaymentPreference']='DEFERRED by 9012';
						$status=2;
						break;
					case '8888':
						$processResult['deferredPaymentPreference']='DEFERRED by 8888';
						$status=3;
						break;
				}
			}
		}

		if (count($errorList)>0){
			$this->_helper->json(array(
				status=>-1,
				messages=>$errorList,
				data=>array()
			));
		}
		else{
				$status=$status?$status:1;

				$messages=Q\Utils::flattenToList($processResult); //mainly for debugging ease, maybe should be removed later

				$purchaseObj=new \Application_Model_Purchase();
				$purchase=$purchaseObj->generate();
				$purchase->chargeTotal=$inData['cardData']['chargeTotal'];
				$purchase->cardName=$inData['cardData']['cardName'];
				$purchase->street=$inData['cardData']['street'];
				$purchase->city=$inData['cardData']['city'];
				$purchase->state=$inData['cardData']['state'];
				$purchase->zip=$inData['cardData']['zip'];
				$purchase->phoneNumber=$inData['cardData']['phoneNumber'];
				$purchase->lastFour=substr($inData['cardData']['cardNumber'], strlen($inData['cardData']['cardNumber'])-4, 4);

				$purchase->deferredPaymentPreference=$processResult['deferredPaymentPreference'];

				$purchase->fdTransactionTime=$processResult['FDGGWSAPI:TRANSACTIONTIME'];
				$purchase->fdProcessorReferenceNumber=$processResult['FDGGWSAPI:PROCESSORREFERENCENUMBER'];
				$purchase->fdProcessorResponseMessage=$processResult['FDGGWSAPI:PROCESSORRESPONSEMESSAGE'];
				$purchase->fdProcessorResponseCode=$processResult['FDGGWSAPI:PROCESSORRESPONSECODE'];
				$purchase->fdProcessorApprovalCode=$processResult['FDGGWSAPI:PROCESSORAPPROVALCODE'];
				$purchase->fdErrorMessage=$processResult['FDGGWSAPI:ERRORMESSAGE'];
				$purchase->fdOrderId=$processResult['FDGGWSAPI:ORDERID'];
				$purchase->fdApprovalCode=$processResult['FDGGWSAPI:APPROVALCODE'];

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
					$order->student=$student;
					$order->offering=$offering;
					$order->day=$day;
					$orderObj->persist(Application_Model_Base::noFlush);
		$orderEntityList[]=$order;
					$purchaseObj->addOrder($order);
				}


				$purchaseObj->persist(Application_Model_Base::yesFlush); //I put "$this->emailReceipt($purchaseObj);" ahead of this line and it stopped persisting!?

				$this->emailReceipt($purchaseObj->entity->refId, $orderEntityList, $status);

			$this->_helper->json(array(
				status=>$status,
				messages=>$messages,
				data=>array(tmp=>'test')
			));


    }
    }

    public function emailReceipt($purchaseRefId, $orderEntityList, $status)
    {
        $auth = \Zend_Auth::getInstance();
		$user=$auth->getIdentity();

// 		$purchaseObj=new Application_Model_Purchase();
// 		$purchaseObj->getByRefId($purchaseRefId);


		$this->doctrineContainer=\Zend_Registry::get('doctrine');
		$this->entityManager=$this->doctrineContainer->getEntityManager();
		$purchaseEntity=$this->entityManager->find('GE\Entity\Purchase', $purchaseRefId);


		$view = new Zend_View();
		$view->setScriptPath(APPLICATION_PATH.'/views/scripts/purchase');

		$view->user=$user;
		$view->purchaseEntity=$purchaseEntity;
		$view->orderEntityList=$orderEntityList;


		$mail = new Zend_Mail();
		$tr=new Zend_Mail_Transport_Sendmail();

		switch($status){
			default:
				$emailMessage=$view->render('email-receipt.phtml');
				$mail->setSubject("Good Earth Lunch Program Purchase Receipt");

				if (!preg_match('/tq/', $user->emailAdr)){

					$list=$orderEntityList;
					$schoolList=array();
					for ($i=0, $len=count($list); $i<$len; $i++){
						$element=$list[$i];

						if ($element->student->school->emailAdr){
							$mail->addCc($element->student->school->emailAdr, $element->student->school->name.' School Lunch Volunteer');
						}
					}

					$mail->addCc('school@genatural.com', 'Sherry Crilly');
					$mail->addBcc('tq@justkidding.com', 'Good Earth Programmer');
				}

				break;
			case '2':
				$emailMessage=$view->render('deferred-email-receipt.phtml');
				$mail->setSubject("Good Earth Lunch Program Invoice");

				if (!preg_match('/tq/', $user->emailAdr)){

					$list=$orderEntityList;
					$schoolList=array();
					for ($i=0, $len=count($list); $i<$len; $i++){
						$element=$list[$i];

						if ($element->student->school->emailAdr){
							$mail->addCc($element->student->school->emailAdr, $element->student->school->name.' School Lunch Volunteer');
						}
					}

					$mail->addCc('school@genatural.com', 'Sherry Crilly');
					$mail->addBcc('tq@justkidding.com');
				}
				break;
			case '3':
				$emailMessage=$view->render('deferred-email-receipt.phtml');
				$mail->setSubject("Good Earth Lunch Program Invoice");

				if (!preg_match('/tq/', $user->emailAdr)){
					$mail->addCc('tq@justkidding.com', 'Good Earth Programmer');
				}

				break;
		}
		$mail->setBodyHtml($emailMessage);
		$mail->setFrom('sherry@genatural.com', "Good Earth Lunch Program");

		$mail->addTo($user->emailAdr, $user->firstName.' '.$user->lastName);

		$mail->send();

		return true;
	}


}
