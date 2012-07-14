<?php

class TestController extends Zend_Controller_Action
{
	private $doctrineContainer;
	private $em;

    public function init()
    {
		$this->doctrineContainer=Zend_Registry::get('doctrine');
		$this->em=$this->doctrineContainer->getEntityManager();
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
    	$this->view->listingArrays=array();

		$this->view->message = "<b>Initializing Things</b><p/>";

		$initSchema=$this->getRequest()->getParam('initSchema');

			$em=$this->em;

		if ($initSchema=='pleaseKillMyData'){

			$this->view->message .= "initializing database schema<br/>";
			$this->view->sqlList=$this->initializeDatabaseSchema();
			$this->view->message .= "database initialization complete<p/>";


			$this->view->message .= "initializing data<br/>";
			$modelObj=$this->_initDays();
				$list=$modelObj->getList($inData);
				$this->view->message .= "-----verified ".count($list)." days<br/>";
				$this->view->listingArrays[]=$list;

			$this->view->message .= $this->_initSchools();


			$schoolObj=new \Application_Model_School();
				$schoolList=$schoolObj->getList($inData);
				$this->view->message .= "-----verified ".count($schoolList)." schools<br/>";
	//			$this->view->listingArrays[]=$schoolList;


			$this->_initGradesLevels();
				$gradeLevelObj=new \Application_Model_GradeLevel();
				$gradeLevelList=$gradeLevelObj->getList($inData);
				$this->view->message .= "-----verified ".count($gradeLevelList)." grade levels<br/>";
	//			$this->view->listingArrays[]=$gradeLevelList;

			$this->_initSchoolGradeLevels();
			$this->view->message .= "-----gradeLevelSchools were initialized<br/>";



		$serverComm=array();
			$serverComm[]=array("fieldName"=>"user_confirm_message", "value"=>$message);
			$serverComm[]=array("fieldName"=>"assert_initial_controller", "value"=>'none');

		$this->view->serverComm=$this->_helper->WriteServerCommDiv($serverComm); //named: Q_Controller_Action_Helper_WriteServerCommDiv

        }
    }

    private function initializeDatabaseSchema(){

			$tool=new \Doctrine\ORM\Tools\SchemaTool($this->em);

			$tool->dropDatabase();
			$this->view->message .= "-----dropped database - bye bye Data!<br/>";

			$metas=$this->em->getMetadataFactory()->getAllMetadata();
			$tool->createSchema($metas);
			$this->view->message .= "-----initializing schema<br/>";

			return $tool->getCreateSchemaSql($metas);
	}

    private function _initSchools(){

	$source=array(
		array('name'=>'Saint Marks'),
		array('name'=>'Lycee Français'),
		array('name'=>'Neil Cummins'),
		array('name'=>'Cascade Canyon'),
		array('name'=>'Marin Horizon'),
		array('name'=>'St Anselm'),
		array('name'=>'Hall Middle School'),
		array('name'=>'Sonoma Academy '),
		array('name'=>'Good Shepherd'),
		array('name'=>'Marin Christian Academy')
	);

	$newObj=new \Application_Model_School();
	$newObj->newFromArrayList($source);
	return $newObj;

    }

private function _initGradesLevels(){
	$source=array(
		array('title'=>'Kindergarten'),
		array('title'=>'First'),
		array('title'=>'Second'),
		array('title'=>'Third'),
		array('title'=>'Fourth'),
		array('title'=>'Fifth'),
		array('title'=>'Sixth')
	);

	$newObj=new \Application_Model_GradeLevel();
	$newObj->newFromArrayList($source);
	return $newObj;
}

private function _initSchoolGradeLevels(){
	//until I can figure out how to make Doctrine do ManyToMany, there will
	//be these stupid join table entities. I'm not making models for them because
	//they should not exist.

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
			$this->em->persist($node);
			$this->em->flush();
		}
	}
}

private function _initDays(){
	$source=array(
		array(title=>'Sun'),
		array(title=>'Mon'),
		array(title=>'Tues'),
		array(title=>'Weds'),
		array(title=>'Thurs'),
		array(title=>'Fri'),
		array(title=>'Sat'),
	);

	$newObj=new \Application_Model_Day();
	$newObj->newFromArrayList($source);
	return $newObj;

}


}









