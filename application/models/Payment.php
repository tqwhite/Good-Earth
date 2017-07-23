<?php
class Application_Model_Payment
{
static function process($purchaseModelList, $inData, $fake=false){
		$paymentList=array();
		$workingList=array();
		for ($i=0, $len=count($purchaseModelList); $i<$len; $i++){
			$paymentData=$purchaseModelList[$i];
			switch ($paymentData->entity->merchantAccountId){
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
// 			$paymentData->entity->chargeTotal=1.03+$i/100; //DEBUG
// 			error_log("FORCING PRICE TO A DOLLAR");
// 			if ($i>0){
// 				$inData['cardData']['cardNumber']='force second trans to faile';
// 			}
			$transactionId=$inData['account']['refId'].'-'.$i.'-'.rand(0,100);
			$paymentObj->setPurchaseData($paymentData->entity, $inData['cardData'], $inData['account'], $transactionId);
			//$result=$paymentObj->executeCharge(array('approval'=>($i==0)?true:false));
			//$result=$paymentObj->authorizeOnly(array('approval'=>($i==0)?true:true));
			$result=$paymentObj->authorizeOnly();
			$workingList[]=array(
				result=>$result,
				paymentObj=>$paymentObj,
				transactionId=>$transactionId
			);
			$showApproved=$result['approved']?'APPROVED':'DECLINED';
				if (!$result['approved']){
					error_log("authorize FAILED: {$transactionId}/{$result['amount']} merchantAccountId:{$paymentData->entity->merchantAccountId}) auth.net({$result['transaction_id']})  {$showApproved} {$result['response_reason_text']} [payment.php]");
				}
				else{
					error_log("authorize: {$transactionId}/{$result['amount']} merchantAccountId:{$paymentData->entity->merchantAccountId}) auth.net({$result['transaction_id']})  {$showApproved} {$result['response_reason_text']} [payment.php]");
				}
			if (!$result['approved']){
				\Application_Model_Payment::voidAuthorizations($workingList);
				return $workingList;
			}
		}
		\Application_Model_Payment::captureTransactions($workingList);
		return $workingList;
}
static function voidAuthorizations($workingList){
		for ($i=0, $len=count($workingList); $i<$len; $i++){
			$element=$workingList[$i];
			if ($element['result']['approved']){
				$result=$element['paymentObj']->voidAuthorization($element['result']['transaction_id']); //transaction_id is the one issued by authorize.net
				if (!$result['approved']){
					error_log("void transaction FAILED: {$element['transactionId']}/{$element['result']['amount']} auth.net({$element['result']['transaction_id']}) {$result['response_reason_text']} [payment.php]");
					error_log("void FAILED: I do not know of any way this can ever happen except for the payment processor failing");
				}
				else{
					error_log("void transaction: {$element['transactionId']}/{$element['result']['amount']} auth.net({$element['result']['transaction_id']}) [payment.php]");
				}
			}
		}
}
static function captureTransactions($workingList){
		for ($i=0, $len=count($workingList); $i<$len; $i++){
			$element=$workingList[$i];
			$result=$element['paymentObj']->captureAuthorized($element['result']['transaction_id'], $element['result']['amount']); //transaction_id is the one issued by authorize.net
				if (!$result['approved']){
					error_log("captureTransactions FAILED: {$element['transactionId']}/{$element['result']['amount']} auth.net({$element['result']['transaction_id']}) {$result['response_reason_text']} [payment.php]");
					error_log("captureTransactions FAILED: I do not know of any way this can ever happen except for the payment processor failing");
				}
				else{
					error_log("captureTransactions: {$element['transactionId']}/{$element['result']['amount']} auth.net({$element['result']['transaction_id']}) [payment.php]");
				}
		}
}
	protected function convertHelixData($data){
		$data['isActiveFlag']=($data['active?']=='Yes')?1:0; unset($data['active?']);
		$data['helixId']=$data['helix id']; unset($data['helix id']);
		return $data;
	}
}
