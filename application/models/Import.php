<?php

class Application_Model_Import
{

public function ping(){
	return $this->importList();
}//end of method

private function importList(){

	return array(
		

//  		new \Application_Model_Day(),
//  		new \Application_Model_School(),
// 		new \Application_Model_GradeLevel(),
// 		new \Application_Model_GradeSchoolNodes(),
// 		
// 		new \Application_Model_Meal(),
// 		
// 		new \Application_Model_Offering(),
// 		new \Application_Model_OfferingDayNodes(),
// 		new \Application_Model_OfferingGradeLevelNodes(),
// 		new \Application_Model_OfferingSchoolNodes()
// 		
// 		,
//   		new \Application_Model_Account(),
//   		new \Application_Model_User(),
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
	
	global $doNotFinishTickle;
	$doNotFinishTickle=false;
	
	global $dbStatusMessages;
	$dbStatusMessages='';
	
	global $dbWriteSuccessMessages;
	$dbWriteSuccessMessages='';
	
	global $dbWriteErrorMessages;
	$dbWriteErrorMessages='';

	$importList=$this->importList(); //returns a literal array of Application_Model objects

	echo "<div style='font-size:18pt;'>Starting Helix->Web data transfer.</div>";
	error_log("Starting import->execute()");
	
	foreach ($importList as $modelObject){
	
		$cleanHelixData=$modelObject->import($inputManager);

// $tmp=\Q\Utils::dumpCliString($cleanHelixData, "cleanHelixData");
// error_log($tmp);

		$dbResult=$modelObject->writeDb($cleanHelixData);

		
		$dbResultArray[$modelObject->entityName]=$dbResult;
	}

		for ($len=count($importList), $i=$len-1; $i>0; $i--){
			$element=$importList[$i];
//			$result=$element->purgeInactive();
			
			echo "$result<br/>";
		}
		
	if ($doNotFinishTickle){
		echo "<div style='color:red;font-size:18pt;margin-top:18pt;'>There were errors. Helix->Web transfer will repeat</div>";
		echo "<div style='color:green;font-size:12pt;margin-bottom:18pt;'>(Any records without errors were loaded into the website.)</div>";
	}
	
	echo "<div style='color:gray;font-size:14pt;margin:18pt;'>$dbStatusMessages</div>";
	echo "<div style='color:green;font-size:14pt;margin:18pt;'>$dbWriteSuccessMessages</div>";
		
	if (!$doNotFinishTickle){
		echo "<div style='color:gray;font-size:14pt;margin:18pt;'>No errors found</div>";
		$inputManager->tickle('end');
		error_log("SUCCESS: Helix->Web transfer is complete");

	}
	else{
		error_log("ERROR: There were errors. Helix->Web transfer will repeat");
		echo "<div style='color:gray;font-size:14pt;margin:18pt;'>$dbWriteErrorMessages</div>";
	}


}//end of method

}//end of class

