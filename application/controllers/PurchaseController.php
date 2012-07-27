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




	//	$errorList=\Application_Model_Student::validate($inData);

		if (false && count($errorList)>0){
			$this->_helper->json(array(
				status=>-1,
				messages=>$errorList,
				data=>array()
			));
		}
		else{

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
					$orderObj->persistAndFlush();

					$orderEntityList[]=$order;
				}


				$purchaseObj=new \Application_Model_Purchase();
				$purchase=$purchaseObj->generate();
				$purchase->transactionId='111';
				$purchase->amountTendered='222';
				$purchaseObj->addOrder($orderEntityList[0]);
				$purchaseObj->addOrder($orderEntityList[1]);
				$purchaseObj->persistAndFlush();

			echo "count=".count($orderEntityList)."\n";
			echo "=====================================\n\n";
			exit;
					$purchaseInitArray=array(
						refId=>$inData['refId'],
						transactionId=>'hello',
						amountTendered=>'111',
						purchaseOrderNodes=>$orderEntityList
					);

					$purchaseObj=new \Application_Model_Purchase();
					$purchase=$purchaseObj->newFromArrayList(array($purchaseInitArray));

			\Doctrine\Common\Util\Debug::dump($purchase);
			exit;

		}
}

    public function XXXpayAction()
    {
		$inData=$this->getRequest()->getPost('data');
		$messages=array();


	$list=$inData['orders'];
	$orderEntityList=array();
	for ($i=0, $len=count($list); $i<$len; $i++){
		$element=$list[$i];

		$studentObj=new \Application_Model_Student();
		$student=$studentObj->getByRefId($element['student']['refId']);
echo "student={$student->firstName}\n";
		$offerObj=new \Application_Model_Offering();
		$offer=$offerObj->getByRefId($element['offer']['refId']);
echo "offer={$offer->name}\n";

		$dayObj=new \Application_Model_Day();
		$day=$dayObj->getByRefId($element['day']['refId']);
echo "day={$day->title}\n";

		$orderInitArray=array(
			refId=>$element['refId'],
			student=>$student,
			offer=>$offer,
			day=>$day
		);

		$orderObj=new \Application_Model_Order();
		$order=$orderObj->newFromArrayList($orderInitArray);
\Doctrine\Common\Util\Debug::dump($order);
		$orderEntityList[]=$order;
	}

echo "count=".count($orderEntityList)."\n";
echo "=====================================\n\n";
exit;
		$purchaseInitArray=array(
			refId=>$inData['refId'],
			transactionId=>'hello',
			amountTendered=>'111',
			purchaseOrderNodes=>$orderEntityList
		);

		$purchaseObj=new \Application_Model_Purchase();
		$purchase=$purchaseObj->newFromArrayList(array($purchaseInitArray));

\Doctrine\Common\Util\Debug::dump($purchase);
exit;

		$errorList=\Application_Model_Student::validate($inData);

		if (count($errorList)>0){
			$this->_helper->json(array(
				status=>-1,
				messages=>$errorList,
				data=>array()
			));
		}
		else{

		$accountObj=new \Application_Model_Account();
		$account=$accountObj->getByRefId($inData['accountRefId']);

		$schoolObj=new \Application_Model_School();
		$school=$schoolObj->getByRefId($inData['schoolRefId']);

		$gradeLevelObj=new \Application_Model_GradeLevel();
		$gradeLevel=$gradeLevelObj->getByRefId($inData['gradeLevelRefId']);

		//NOTE: $inData has properties named 'schoolRefId'. I demonstrated that Doctrine ignores them.

		$inData['school']=$school;
		$inData['gradeLevel']=$gradeLevel;
		$inData['account']=$account;

		$studentObj=new \Application_Model_Student();
		$student=$studentObj->getByRefId($inData['refId']);

			$status=1; //unless error
			try{

				if (count($student)==0){
					$studentObj->newFromArrayList(array($inData), false);
				}
				else{

					$studentObj->updateFromArray($student, $inData);
				}
			}
			catch(Exception $e){
				$status=-1;
				$messages[]=array('server_error', $e->errorInfo);
			}

			$this->_helper->json(array(
				status=>$status,
				messages=>$messages,
				data=>array(
					"identity"=>array(
						'firstName'=>$inData['firstName'],
						'lastName'=>$inData['lastName']
					)
				)
			));


    }
}


}



