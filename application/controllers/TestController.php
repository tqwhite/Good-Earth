<?php

class TestController extends Zend_Controller_Action
{

    public function init()
    {
		/* Initialize action controller here */
    }

    public function indexAction()
    {
		// action body
    }

    public function databaseAction()
    {

    echo "DATABASE\n";
		$locale=$this->getRequest()->getParam('locale');

	switch ($locale){
		case 'qDev':
			$db = new Zend_Db_Adapter_Pdo_Mysql(array(
				'host'     => '127.0.0.1',
				'username' => 'tq',
				'password' => '',
				'dbname'   => 'test1'
			));
		break;
		case 'demo':
			$db = new Zend_Db_Adapter_Pdo_Mysql(array(
				'host'     => 'localhost',
				'username' => 'goodearthsite',
				'password' => 'glory*snacks',
				'dbname'   => 'goodEarthDemoData'
			));
		break;

		}

		$stmt = $db->query('select * from example');

		print_r($stmt->fetch());
		echo '<p/>'.Zend_Version::VERSION;
    }

    public function doctrineAction()
    {

		$this->doctrineContainer=Zend_Registry::get('doctrine');
    	$firstName='Jimmie';
		$lastName='Doe';

		$accountObj=new GE\Entity\Account();
		$accountObj->familyName='Does';

        $testObj = new GE\Entity\Student();
        $testObj->firstName = $firstName;
        $testObj->lastName = $lastName;
        $testObj->account=$accountObj;

		$em=$this->doctrineContainer->getEntityManager();
		$em->persist($testObj);
		$em->flush();


/*
this works
		$this->doctrineContainer=Zend_Registry::get('doctrine');

		$u=new GE\Entity\User();
		$u->firstName='tq';
		$u->lastName='white';
		$u->userName='tq'.  uniqid();
		$u->password='12345';
		$u->userName='tqwhite';

		$em=$this->doctrineContainer->getEntityManager();
		$em->persist($u);
		$em->flush();
		$em->clear();

		$users=$em
			->createQuery('select u from GE\Entity\User u')
			->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
*/
/*
		foreach ($users as $user){
		    echo 'refId='.$user->refId.'<br/>';
		}
*/
		$serverComm=array();
			$serverComm[]=array("fieldName"=>"user_confirm_message", "value"=>'test complete');
			$serverComm[]=array("fieldName"=>"assert_initial_controller", "value"=>'none');

		$this->view->serverComm=$this->_helper->WriteServerCommDiv($serverComm); //named: Q_Controller_Action_Helper_WriteServerCommDiv




    }

    public function sqliteAction()
    {
		$db = new SQLite3('mysqlitedb.db');
		print_r($db);
    }

    public function initAction()
    {
		$this->view->message = "<b>Initializing Things</b><p/>";

		$initSchema=$this->getRequest()->getParam('initSchema');

		$this->doctrineContainer=Zend_Registry::get('doctrine');
			$em=$this->doctrineContainer->getEntityManager();

		if ($initSchema=='pleaseKillMyData'){
			$tool=new \Doctrine\ORM\Tools\SchemaTool($em);

			$tool->dropDatabase();
			$this->view->message .= "dropped database - bye bye Data!<br/>";

			$metas=$em->getMetadataFactory()->getAllMetadata();
			$tool->createSchema($metas);
			$this->view->message .= "initializing database<br/>";

			$this->view->sqlList=$tool->getCreateSchemaSql($metas);

			$this->view->message .= "initializing school data<br/>";
			$this->_initSchools();

			$schoolObj=new \Application_Model_School();
			$schoolList=$schoolObj->getList($inData);

			$this->view->schoolList=$schoolList;

			$gradeLevelObj=new \Application_Model_GradeLevel();
			$gradeLevelList=$gradeLevelObj->getList($inData);

			$this->view->gradeLevelList=$gradeLevelList;

		$serverComm=array();
			$serverComm[]=array("fieldName"=>"user_confirm_message", "value"=>$message);
			$serverComm[]=array("fieldName"=>"assert_initial_controller", "value"=>'none');

		$this->view->serverComm=$this->_helper->WriteServerCommDiv($serverComm); //named: Q_Controller_Action_Helper_WriteServerCommDiv

        }
    }

    private function _initSchools(){


		$this->doctrineContainer=Zend_Registry::get('doctrine');
		$em=$this->doctrineContainer->getEntityManager();

		$u=new GE\Entity\School();
				$u->name='Saint Marks';
				$em->persist($u);
				$em->flush();


		$u=new GE\Entity\School();
				$u->name='Lycee Français';
				$em->persist($u);
				$em->flush();


		$u=new GE\Entity\School();
				$u->name='Neil Cummins';
				$em->persist($u);
				$em->flush();


		$u=new GE\Entity\School();
				$u->name='Cascade Canyon';
				$em->persist($u);
				$em->flush();


		$u=new GE\Entity\School();
				$u->name='Marin Horizon';
				$em->persist($u);
				$em->flush();


		$u=new GE\Entity\School();
				$u->name='St Anselm';
				$em->persist($u);
				$em->flush();


		$u=new GE\Entity\School();
				$u->name='Hall Middle School';
				$em->persist($u);
				$em->flush();


		$u=new GE\Entity\School();
				$u->name='Sonoma Academy ';
				$em->persist($u);
				$em->flush();


		$u=new GE\Entity\School();
				$u->name='Good Shepherd';
				$em->persist($u);
				$em->flush();


		$u=new GE\Entity\School();
				$u->name='Marin Christian Academy';
				$em->persist($u);
				$em->flush();





		$u=new GE\Entity\GradeLevel();
		$u->title='Kindergarten';
		$em->persist($u);
		$em->flush();

		$u=new GE\Entity\GradeLevel();
		$u->title='First';
		$em->persist($u);
		$em->flush();

		$u=new GE\Entity\GradeLevel();
		$u->title='Second';
		$em->persist($u);
		$em->flush();

		$u=new GE\Entity\GradeLevel();
		$u->title='Third';
		$em->persist($u);
		$em->flush();

		$u=new GE\Entity\GradeLevel();
		$u->title='Fourth';
		$em->persist($u);
		$em->flush();

		$u=new GE\Entity\GradeLevel();
		$u->title='Fifth';
		$em->persist($u);
		$em->flush();

		$u=new GE\Entity\GradeLevel();
		$u->title='Sixth';
		$em->persist($u);
		$em->flush();


		$em->clear();

		$this->_initSchoolGradeLevels();

    }

private function _initSchoolGradeLevels(){

		$this->doctrineContainer=Zend_Registry::get('doctrine');
		$em=$this->doctrineContainer->getEntityManager();


		$schoolObj=new \Application_Model_School();
		$schoolList=$schoolObj->getList('record');

		$gradeLevelObj=new \Application_Model_GradeLevel();
		$gradeLevelList=$gradeLevelObj->getList('record');

		foreach ($schoolList as $school){

			foreach ($gradeLevelList as $gradeLevel){


				$node=new GE\Entity\GradeSchoolNode();
				$node->school=$school;
				$node->gradeLevel=$gradeLevel;
				$node->descriptor=$school->name.'/'.$gradeLevel->title;
				$em->persist($node);
				$em->flush();



			}


		}



		/*=======================================

	for ($i=0, $len=count($schoolList); $i<$len; $i++){
		$school=$schoolList[$i];

echo 'name='.$school->name."</br>";

	for ($j=0, $len2=count($gradeLevelList); $j<$len2; $j++){
		$gradLevel=$gradeLevelList[$j];



				$node=new GE\Entity\GradeSchoolNode();
				$node->school=$school;
				$node->gradeLevel=$gradLevel;
				$em->persist($u);
				$em->flush();



			}


		}
*/

}


}









