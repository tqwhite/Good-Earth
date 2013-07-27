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
					array('nothing'=>'hello')
				);
}

public function read($args){

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
	
	return $outResult['data'];

}//end of method

}//end of class
    



