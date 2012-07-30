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


		$errorList=\Application_Model_Purchase::validate($inData);

		if (count($errorList)==0){
			$processResult=\Application_Model_Payment::process($inData);
			if ($processResult['FDGGWSAPI:TRANSACTIONRESULT']!='APPROVED'){
				if ($processResult['DETAIL']){
					$errorList[]=array('transaction', preg_replace('/^.*: /', '', $processResult['DETAIL']));
				}
				else{
					$errorList[]=array('transaction', preg_replace('/^.*: /', '', $processResult['FDGGWSAPI:ERRORMESSAGE']));
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
				$status=1;

				$messages=Q\Utils::flattenToList($processResult); //mainly for debugging ease, maybe should be removed later

				$purchaseObj=new \Application_Model_Purchase();
				$purchase=$purchaseObj->generate();
				$purchase->chargeTotal=$inData['cardData']['chargeTotal'];
				$purchase->chargeTotal=$inData['cardData']['billingName'];
				$purchase->chargeTotal=$inData['cardData']['street'];
				$purchase->chargeTotal=$inData['cardData']['city'];
				$purchase->chargeTotal=$inData['cardData']['state'];
				$purchase->chargeTotal=$inData['cardData']['zip'];
				$purchase->chargeTotal=$inData['cardData']['phoneNumber'];
				$purchase->lastFour=substr($inData['cardData']['cardNumber'], strlen($inData['cardData']['cardNumber'])-4, 4);

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

				$this->emailReceipt($purchaseObj->entity->refId, $orderEntityList);

			$this->_helper->json(array(
				status=>$status,
				messages=>$messages,
				data=>array(tmp=>'test')
			));


    }
    }

    public function emailReceipt($purchaseRefId, $orderEntityList)
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

		$emailMessage=$view->render('email-receipt.phtml');

		$mail = new Zend_Mail();
		$tr=new Zend_Mail_Transport_Sendmail();


		$mail->setBodyHtml($emailMessage);
		$mail->setFrom('sherry@genatural.com', "Good Earth Lunch Program");

		$mail->setSubject("Good Earth: Lunch Program Purchase Receipt");

		$mail->addTo('tq@justkidding.com', 'TQ White II');


		$mail->send();

		return true;
	}


}





