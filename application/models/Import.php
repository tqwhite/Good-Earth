<?php

class Application_Model_Import
{

public function ping(){
	return $this->importList();
}//end of method

private function importList(){

	return array(

// 		new \Application_Model_Day(),
 		new \Application_Model_School(),
// 		new \Application_Model_GradeLevel(),
//		new \Application_Model_Account(),
//		new \Application_Model_User(),
//		new \Application_Model_Student(),
// 		new \Application_Model_Meal(),
// 		new \Application_Model_Offering(),
// 		new \Application_Model_OfferingDayNodes(),
// 		new \Application_Model_OfferingGradeLevelNodes(),
// 		new \Application_Model_OfferingSchoolNodes(),
//		new \Application_Model_GradeSchoolNodes()

	);
}//end of method



public function execute(){
	$inputManager=new \Heliport\InputManager();
//	$inputManager->tickle('start');

	$importList=$this->importList(); //returns a literal array of Application_Model objects
	
	foreach ($importList as $modelObject){
	
		$cleanHelixData=$modelObject->import($inputManager);

		$dbResult=$modelObject->writeDb($cleanHelixData);
		
		$dbResultArray[$modelObject->entityName]=$dbResult;
	}
	
\Q\Utils::dumpWeb($dbResultArray, "dbResultArray");

//	$inputManager->tickle('end');
}//end of method

}//end of class
