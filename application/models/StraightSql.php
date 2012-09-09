<?php

class Application_Model_StraightSql
{

    static function update($args)
    {



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
\Q\Utils::dumpWeb($args['tableName']);
\Q\Utils::dumpWeb($args['updateSetData']);
\Q\Utils::dumpWeb($args['whereClauses']);

	$result=$db->update($args['tableName'], $args['updateSetData'], $args['whereClauses']);
\Q\Utils::dumpWeb($result);
exit;
		return;

    }

}

