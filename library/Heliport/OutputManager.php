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
		foreach ($inData as $label=>$data){
$element=$data;
ksort($element); //this is by reference,  it changes $element

echo "tableName=$tableName<br/>";
\Zend_Debug::dump($element);
echo "<div style='background:red;height:10px;width:100%;'></div>";
		$element=$this->removeNulls($element);
/*
			$resultSet = $this->connection->store(
			"  inert process",
			$tableName,
			$element
			);
*/
/*
			foreach ($element as $label=>$data){
				$sql="update $tableName set alreadyInHelix='1' where refId = '{$element['refId']}'";
				\Application_Model_StraightSql::update($sql);
				echo $sql;

			}
*/
		}

    }
private function removeNulls($inArray){
	$outArray=array();
	foreach ($inArray as $label=>$data){
			if (is_null($data)){
				$outArray[$label]='';
			}
			else{
				$outArray[$label]=$data;
			}
	echo "------$label={$outArray[$label]}<BR/";
		}
	return $outArray;;
}

}
