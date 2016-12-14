<?php

class Application_Model_Import
{

public function ping(){
	return $this->importList();
}//end of method

private function importList(){

	return array(
		

 		new \Application_Model_Day(),
 		new \Application_Model_School(),
		new \Application_Model_GradeLevel(),
		new \Application_Model_GradeSchoolNodes(),
		
		new \Application_Model_Meal(),
		
		new \Application_Model_Offering(),
		new \Application_Model_OfferingDayNodes(),
		new \Application_Model_OfferingGradeLevelNodes(),
		new \Application_Model_OfferingSchoolNodes()
		
		,
 		new \Application_Model_Account(),
 		new \Application_Model_User(),
 		new \Application_Model_Student()

	);
}//end of method

private function hold(){

	return array(

//  		new \Application_Model_Day(),
//  		new \Application_Model_School(),
// 		new \Application_Model_GradeLevel(),
// 		new \Application_Model_GradeSchoolNodes(),
// 		
// 		new \Application_Model_Meal(),
// 		
// 		new \Application_Model_Account(),
// 		new \Application_Model_User(),
// 		new \Application_Model_Student(),
// 		
// 		new \Application_Model_Offering(),
// 		new \Application_Model_OfferingDayNodes(),
// 		new \Application_Model_OfferingGradeLevelNodes(),
// 		new \Application_Model_OfferingSchoolNodes()

	);

	return array(

//  		new \Application_Model_Day(),
//  		new \Application_Model_School(),
// 		new \Application_Model_GradeLevel(),
// 		
// 		new \Application_Model_Meal(),
// 		
// 		new \Application_Model_Account(),
// 		new \Application_Model_User(),
// 		new \Application_Model_Student(),
// 		
// 		new \Application_Model_Offering(),
// 		
// 		new \Application_Model_OfferingDayNodes(),
// 		new \Application_Model_OfferingGradeLevelNodes(),
// 		new \Application_Model_OfferingSchoolNodes(),
// 		new \Application_Model_GradeSchoolNodes()

	);
}//end of method



public function execute(){
	$inputManager=new \Heliport\InputManager();
	$inputManager->tickle('start');

	$importList=$this->importList(); //returns a literal array of Application_Model objects

	echo "The data shown below is THE DATA THAT AS IT WAS RECEIVED FROM HELIX. The names have been mapped but no data conversion has been done. To see what went to the database, look in the database.<br/><br/>\n\n";
error_log("Starting import->execute()");
	
	foreach ($importList as $modelObject){
	
		$cleanHelixData=$modelObject->import($inputManager);

$tmp=\Q\Utils::dumpCliString($cleanHelixData, "cleanHelixData");
error_log($tmp);

		$dbResult=$modelObject->writeDb($cleanHelixData);

		
		$dbResultArray[$modelObject->entityName]=$dbResult;
	}

		for ($len=count($importList), $i=$len-1; $i>0; $i--){
			$element=$importList[$i];
//			$result=$element->purgeInactive();
			
			echo "$result<br/>";
		}
	
	\Q\Utils::dumpWeb($dbResultArray, "dbResultArray");

	$inputManager->tickle('end');


}//end of method

}//end of class

