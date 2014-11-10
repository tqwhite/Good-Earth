<?php
namespace Heliport;

class InputManager
{

private $connection;

public function __construct(){
	
	$this->initHelix();
}

public function __destruct(){
	$this->connection->releasePoolUser();
}

private function initHelix(){
	$this->connection=new ServerInterface();
	$helix_status = $this->connection->ihr190();
// 	if (!$this->helix_status){
// 		die("<div style='color:red;font-size:24pt;'>InputManager says: Helix is down</div>");
// 	}
	$this->connection->leasePoolUser();
}

public function setHelixExportThreshold($threshold='7-1-00  20:29:27'){

	$this->connection->store(
					'  inert process',
					'export_end',
					array(
					'blank1'=>'hello',
					'blank2'=>'hello',
					'startrun'=>$threshold
					)
				);
}

// public function releasePoolUsers(){
// 
// 	$this->connection->retrieve(
// 					'  user pool global',
// 					'Release All Pool Users',
// 					'export_end'
// 				);
// }

public function tickle($whichSide){
	if ($whichSide=='start'){
		$tickler='export_start';
	}
	else{
		$tickler='export_end';
	}
	
	
	$this->connection->store(
					'  inert process',
					$tickler,
					array('blank1'=>'hello','blank2'=>'hello')
				);
}

public function read($args){

	
	error_log("inputManager::read() - STARTING - {$args['relationName']} {$args['viewName']}");	
	if (gettype($args['queryData'])=='Array'){
			$resultSet = $this->connection->hc->store(
				$args['queryData']['relationName'],
				$args['queryData']['viewName'],
				$args['queryData']['argsArray']
			);
	}
	
	$startTime=time();

	$outResult = $this->connection->retrieve(
		$args['relationName'],
		$args['viewName']
	);
	
	$endTime=time();
	
	if ($endTime-$startTime>20 && count($outResult['data'])==0){
		$this->connection->releasePoolUser();
		$message="<div style='color:red;font-size:24pt;margin:36px 0px;'>InputManager::read() says, {$args['relationName']}/{$args['viewName']} took too long (".($endTime-$startTime)." seconds) and didn't return any data.</div>";
		error_log($message);
		$this->sendDeathEmail($message);
		die($message);
	}
	
	error_log("inputManager::read() - ENDING - {$args['relationName']} {$args['viewName']} ".count($outResult)." records ".($endTime-$startTime)." seconds");
	
	return $outResult['data'];

}//end of method

private function sendDeathEmail(){

$tr=new \Zend_Mail_Transport_Sendmail($message);
		\Zend_Mail::setDefaultTransport($tr);
		\Zend_Mail::setDefaultFrom('school@genatural.com', "Good Earth Lunch Program");
		\Zend_Mail::setDefaultReplyTo('school@genatural.com', "Good Earth Lunch Program");

//			$addressList[]=array('name'=>'Good Earth Organic School Lunch Program', 'address'=>'school@genatural.com', 'type'=>'accounting');

		$addressList[]=array('name'=>'Website Programmer', 'address'=>'tq@justkidding.com', 'type'=>'accounting');


		$emailMessage=$message;
		$emailSubject="Good Earth Website Import Error Notification";


		for ($i=0, $len=count($addressList); $i<$len; $i++){
			$element=$addressList[$i];
			$mail = new \Zend_Mail();
			$mail->setSubject($emailSubject);
			$mail->setBodyHtml($emailMessage);

			$mail->addTo($element['address'], $element['name']);

			$mail->send($tr);

		}


		\Zend_Mail::clearDefaultFrom();
		\Zend_Mail::clearDefaultReplyTo();
}

}//end of class
    



