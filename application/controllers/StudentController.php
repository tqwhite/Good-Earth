<?php

class StudentController extends Q_Controller_Base
{

    public function init()
    {
        
        parent::updateAuditInfo($this->getFileName());
    }

    public function indexAction()
    {
        // action body
    }

    public function addAction()
    {
		$inData=$this->getRequest()->getPost('data');
		$messages=array();


		$errorList=\Application_Model_Student::validate($inData);

		if (count($errorList)>0){
			$this->_helper->json(array(
				status=>-1,
				messages=>$errorList,
				data=>array()
			));
		}
		else{

		$accountObj=new \Application_Model_Account();
		$account=$accountObj->getByRefId($inData['accountRefId']);

		$schoolObj=new \Application_Model_School();
		$school=$schoolObj->getByRefId($inData['schoolRefId']);

		$gradeLevelObj=new \Application_Model_GradeLevel();
		$gradeLevel=$gradeLevelObj->getByRefId($inData['gradeLevelRefId']);

		//NOTE: $inData has properties named 'schoolRefId'. I demonstrated that Doctrine ignores them.

		$inData['school']=$school;
		$inData['gradeLevel']=$gradeLevel;
		$inData['account']=$account;

		$studentObj=new \Application_Model_Student();
		$student=$studentObj->getByRefId($inData['refId']);

			$status=1; //unless error
			try{

				if (count($student)==0){
					$studentObj->newFromArrayList(array($inData), false);
				}
				else{

					$studentObj->updateFromArray($student, $inData);
				}
			}
		
		
			catch(Exception $e){
				$status=-1;
				$messages[]=array('server_error', $e->errorInfo);
			}
			
		$student->account->alreadyInHelix=false;

// 					$this->doctrineContainer = Zend_Registry::get('doctrine');
// 					$em = $this->doctrineContainer->getEntityManager();
// 					$em->persist($student);
// 					$em->flush();
		

			$this->_helper->json(array(
				status=>$status,
				messages=>$messages,
				data=>array(
					"identity"=>array(
						'firstName'=>$inData['firstName'],
						'lastName'=>$inData['lastName'],
						'vegetarianFlag'=>$inData['vegetarianFlag'],
						'isTeacherFlag'=>$inData['isTeacherFlag'],
						'allergyFlag'=>$inData['allergyFlag']
					),
					"student"=>$studentObj->formatOutput($student)
				)
			));


    }
}


}



