<?php
namespace Heliport;

class OutputManager
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

public function write($tableName, $inData)
    {
		for ($i=0, $len=count($inData); $i<$len; $i++){
			$element=$inData[$i];
			\Q\Utils::dumpWeb($element);
/*
			$resultSet = $this->connection->store(
			"  inert process",
			$tableName,
			$element
			);
*/
		}

    }

}
