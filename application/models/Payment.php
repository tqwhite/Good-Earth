<?php

class Application_Model_Payment
{


static function process($purchaseModelList, $inData){
		$paymentList=array();
		


		for ($i=0, $len=count($purchaseModelList); $i<$len; $i++){
			$paymentData=$purchaseModelList[$i];
		
			switch ($paymentData->merchantAccountId){
				case '10':
					$paymentObj=new \Payment\Authorize();
				break;
				case '11':
					$paymentObj=new \Payment\Authorize();
				break;
				default:
					$paymentObj=new \Payment\Authorize();
				break;
			
			}

			$paymentObj->setPurchaseData($paymentData->entity, $inData['cardData'], $inData['account']);
		}

		
	//$result=$paymentObj->executeCharge();
	
	$result=array(
	approved=>'APPROVED',
	authorization_code=>'no processing occurs yet, payment.php'
	
	);


	return $result;
}
	
	protected function convertHelixData($data){
		$data['isActiveFlag']=($data['active?']=='Yes')?1:0; unset($data['active?']);
		$data['helixId']=$data['helix id']; unset($data['helix id']);

		return $data;
	}

}

