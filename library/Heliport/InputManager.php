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

	
	error_log("inputManager::read() - STARTING - {$args['relationName']} {$args['viewName']} logEnd");	
	if (gettype($args['queryData'])=='Array'){
			$resultSet = $this->connection->hc->store(
				$args['queryData']['relationName'],
				$args['queryData']['viewName'],
				$args['queryData']['argsArray']
			);
	}

	$outResult = $this->connection->retrieve(
		$args['relationName'],
		$args['viewName']
	);
	
	error_log("inputManager::read() - ENDING - {$args['relationName']} {$args['viewName']} logEnd");
	
	return $outResult['data'];

}//end of method

}//end of class
    



