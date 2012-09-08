<?php

class Application_Model_StraightSql
{

    static function update($args)
    {

	$args=array(
		'updateSetData'=>array(
			'alreadyInHelix'=>1
			),
		'tableName'=>'purchases',
		'whereClauses'=>array(
			"refId='5727B2F9-46A6-0BCD-65C1-4B1C328C5EBB'"
		)
	);

		$locale='qDev';
	switch ($locale){
		default:
		case 'qDev':
		echo "starting qDev";
			$db = new Zend_Db_Adapter_Pdo_Mysql(array(
				'host'     => 'localhost',
				'username' => 'tq',
				'password' => '',
				'dbname'   => 'test1'
			));
		break;
		case 'demo':
			$db = new Zend_Db_Adapter_Pdo_Mysql(array(
				'host'     => '69.195.198.238',
				'username' => 'qbook',
				'password' => 'glory*snacks',
				'dbname'   => 'goodEarthDemoData'
			));
		break;

		}
$result=$db->fetchAll("select * from schools");
\Q\Utils::dumpWeb($result);
	$result=$db->update($args['tableName'], $args['updateSetData'], $args['whereClauses']);
\Q\Utils::dumpWeb($result); exit;
		return;

    }

}

