<?php

class Application_Model_Account extends Application_Model_Base
{

	const entityName="Account";

	public function __construct(){
		parent::__construct();
	}

	static function validate($inData){

		$errorList=array();

		$name='userName';
		$datum=$inData[$name];
		if (strlen($datum)<6){
			$errorList[]=array($name, "User Name too short");
		}

		$name='password';
		$datum=$inData[$name];
		if (strlen($datum)<6){
			$errorList[]=array($name, "Password too short");
		}

		return $errorList;
	}

	static function formatOutput($inData, $outputType){

		$users=Q\Utils::buildArray($inData->users, 'firstName lastName');
		$students=array();

		foreach ($inData->students as $data){
			$students[]=\Application_Model_Student::formatOutput($data);
		}

		switch ($outputType){

			case 'limited':
				$outArray=array(
						'refId'=>$inData->refId
					);
				break;
			default:
				$outArray=array(
						'refId'=>$inData->refId,
						'familyName'=>$inData->familyName,
						'users'=>\Application_Model_User::formatOutput($inData->users, 'limited'),
						'students'=>\Application_Model_Student::formatOutput($inData->students, $outputType)
					);
				break;
		}


		return $outArray;

	}
/*

						'users'=>\Application_Model_User::formatOutput($inData->users),
						'students'=>\Application_Model_Student::formatOutput($inData->students)
*/

}

