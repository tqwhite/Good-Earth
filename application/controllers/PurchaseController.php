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

					$purchaseObj->addOrder($order);
				}

				$purchaseObj->persist(Application_Model_Base::yesFlush);


			$this->_helper->json(array(
				status=>$status,
				messages=>$messages,
				data=>array(tmp=>'test')
			));

		}
	}


}



