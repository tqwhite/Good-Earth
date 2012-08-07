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
echo "disabled"; exit;
$cardNumber="4005550000000019"; //only approve for one dollar even, remember to void transaction
$expMonth="12";
$expYear="13";
$chargeTotal="1";
$acctPassword="WS1001178130._.1:Gh8daJgG";

$ch =curl_init("https://ws.firstdataglobalgateway.com/fdggwsapi/services/order.wsdl");
$pemPath="../library/Credentials/FDGGWS_Certificate_WS1001178130._.1/WS1001178130._.1.pem";
$keyPath="../library/Credentials/FDGGWS_Certificate_WS1001178130._.1/WS1001178130._.1.key";
$sslPw="ckp_1343424713";

$body = "
	<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\">
		<SOAP-ENV:Header />
		<SOAP-ENV:Body>
			<fdggwsapi:FDGGWSApiOrderRequest xmlns:fdggwsapi=\"http://secure.linkpt.net/fdggwsapi/schemas_us/fdggwsapi\">
				<v1:Transaction xmlns:v1=\"http://secure.linkpt.net/fdggwsapi/schemas_us/v1\">
					<v1:CreditCardTxType>
						<v1:Type>sale</v1:Type>
					</v1:CreditCardTxType>
					<v1:CreditCardData>
						<v1:CardNumber>$cardNumber</v1:CardNumber>
						<v1:ExpMonth>$expMonth</v1:ExpMonth>
						<v1:ExpYear>$expYear</v1:ExpYear>
					</v1:CreditCardData>
					<v1:Payment>
						<v1:ChargeTotal>$chargeTotal</v1:ChargeTotal>
					</v1:Payment>
				</v1:Transaction>
			</fdggwsapi:FDGGWSApiOrderRequest>
		</SOAP-ENV:Body>
	</SOAP-ENV:Envelope>
";


curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSLCERT, $pemPath);
curl_setopt($ch, CURLOPT_SSLKEY, $keyPath);
curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $sslPw);

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $acctPassword);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
curl_close($ch);

	$xml=xml_parser_create('');
	$values=array();
	$index=array();
	xml_parse_into_struct($xml, $result, &$values, &$index);
	$outList=array();
	for ($i=0, $len=count($values); $i<$len; $i++){
		$outList[$values[$i]['tag']]=$values[$i]['value'];
	}
Q\Utils::dumpWeb($outList);


exit;
    }

    public function offeringsDevelopmentMethod()
    {

		$mealSourceArrayList=array(array(
			name=>'Pizza Galore2',
			description=>'test description'
		));
		$mealObj=new \Application_Model_Meal();
		$mealEntityList=$mealObj->newFromArrayList($mealSourceArrayList, false);
		$mealEntity=$mealEntityList[0]; //newFromArrayList() produces an array, even if only one
echo 'Meal='.$mealEntity->name."<br/>";


// 		$mealObj=new \Application_Model_Meal();
//  		$mealList=$mealObj->getByRefId('50025aa9da897');
//  		$mealEntity=$mealList;
// echo 'Meal='.$mealEntity->name."<br/>";

		$dayObj=new \Application_Model_Day();
		$dayEntity=$dayObj->getByRefId('3');
echo 'Day='.$dayEntity->title."<br/>";

		$schoolObj=new \Application_Model_School();
		$schoolEntity=$schoolObj->getByRefId('MarinHorizon');
echo 'School='.$schoolEntity->name."<br/>";

		$gradeLevelObj=new \Application_Model_GradeLevel();
		$gradeLevelEntity=$gradeLevelObj->getByRefId('First');
echo 'GradeLevel='.$gradeLevelEntity->title."<br/>";



		$offeringSourceArrayList=array(array(
			comment=>'test comment',
			suggestedPrice=>100,
			name=>'testOfferingName'.\Q\Utils::newGuid(),
			meal=>$mealEntity,
			school=>'MarinHorizon',
			day=>'3',
			gradeLevel=>'First'
		));
		$offeringObj=new \Application_Model_Offering();
		$offeringEntityList=$offeringObj->newFromArrayList($offeringSourceArrayList, false);
		$offeringEntity=$offeringEntityList[0]; //newFromArrayList() produces an array, even if only one
 echo 'Offering='.$offeringEntity->name."<br/>";

 		$this->em->flush();

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
    	error_reporting(E_ERROR | E_WARNING | E_PARSE);

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
	//			$this->view->listingArrays[]=$list;

			$this->view->message .= 'initializing schools<br/>';
			$this->_initSchools();

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
			$this->view->message .= "-----gradeLevelSchools were initialized<p/>";

set_time_limit(3600);

			$this->view->message .= "initializing meals and offerings test data<br/>";
		$mealArray=$this->genMealArray();

			$mealObj=new \Application_Model_Meal();
			$mealEntityList=$mealObj->newFromArrayList($mealArray, false);
			$this->view->message .= "-----created ".count($mealEntityList)." meals<br/>";

		$offeringArray=$this->genOfferingArray();

		$offeringObj=new \Application_Model_Offering();
		$offeringEntityList=$offeringObj->newFromArrayList($offeringArray, false);
			$this->view->message .= "-----created ".count($offeringEntityList)." offerings<br/>";

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
		array('name'=>'Cascade Canyon', refId=>'9'),
		array('name'=>'Good Shepherd', refId=>'34'),
		array('name'=>'Greenwood', refId=>'5'),
		array('name'=>'Hall Middle School', refId=>'29', emailAdr=>'Hall-hallhotlunch@gmail.com'),
		array('name'=>'Lycee Francais', refId=>'21'),
		array('name'=>'Marin Horizon', refId=>'8'),
		array('name'=>'Neil Cummins', refId=>'22', emailAdr=>'nchotlunch@gmail.com'),
		array('name'=>'Saint Marks', refId=>'3'),
		array('name'=>'Sonoma Academy', refId=>'33'),
		array('name'=>'St Anselm', refId=>'28')
	);

	$newObj=new \Application_Model_School();
	$newObj->newFromArrayList($source, false);
	return $newObj;

    }

	private function _initGradesLevels(){
		$source=array(
			array('title'=>'p', 'refId'=>'p', 'seqNum'=>'20'),
			array('title'=>'pk1', 'refId'=>'pk1', 'seqNum'=>'30'),
			array('title'=>'pk2', 'refId'=>'pk2', 'seqNum'=>'40'),
			array('title'=>'pk3', 'refId'=>'pk3', 'seqNum'=>'50'),
			array('title'=>'k', 'refId'=>'k', 'seqNum'=>'60'),
			array('title'=>'1', 'refId'=>'1', 'seqNum'=>'70'),
			array('title'=>'2', 'refId'=>'2', 'seqNum'=>'80'),
			array('title'=>'3', 'refId'=>'3', 'seqNum'=>'90'),
			array('title'=>'4', 'refId'=>'4', 'seqNum'=>'100'),
			array('title'=>'5', 'refId'=>'5', 'seqNum'=>'110'),
			array('title'=>'6', 'refId'=>'6', 'seqNum'=>'120'),
			array('title'=>'7', 'refId'=>'7', 'seqNum'=>'130'),
			array('title'=>'8', 'refId'=>'8', 'seqNum'=>'140'),
			array('title'=>'9', 'refId'=>'9', 'seqNum'=>'150'),
			array('title'=>'10', 'refId'=>'10', 'seqNum'=>'160'),
			array('title'=>'11', 'refId'=>'11', 'seqNum'=>'170'),
			array('title'=>'12', 'refId'=>'12', 'seqNum'=>'180')
		);

		$newObj=new \Application_Model_GradeLevel();
		$newObj->newFromArrayList($source, false);
		return $newObj;
	}

	private function _initSchoolGradeLevels(){
		//until I can figure out how to make Doctrine do ManyToMany, there will
		//be these stupid join table entities. I'm not making models for them because
		//they should not exist.

		$schoolGradeLevelList=$this->getSchoolGradeLevelArray();

		$schoolObj=new \Application_Model_School();
		$schoolList=$schoolObj->getList('record');

		$gradeLevelObj=new \Application_Model_GradeLevel();
		$gradeLevelList=$gradeLevelObj->getList('record');

		foreach ($schoolGradeLevelList as $item){
			$schoolObj=new \Application_Model_School();
			$school=$schoolObj->getByRefId($item['schoolRefId']);

			$gradeLevelObj=new \Application_Model_GradeLevel();
			$gradeLevel=$gradeLevelObj->getByRefId($item['gradeLevelRefId']);

			$node=new GE\Entity\GradeSchoolNode();
			$node->school=$school;
			$node->gradeLevel=$gradeLevel;
			$this->em->persist($node);
			$this->em->flush();
		}
	}

	private function _initDays(){
		$source=array(
			array(title=>'Mon', 'refId'=>'1', 'seqNum'=>'10'),
			array(title=>'Tues', 'refId'=>'2', 'seqNum'=>'20'),
			array(title=>'Weds', 'refId'=>'3', 'seqNum'=>'30'),
			array(title=>'Thurs', 'refId'=>'4', 'seqNum'=>'40'),
			array(title=>'Fri', 'refId'=>'5', 'seqNum'=>'50')
		);

		$newObj=new \Application_Model_Day();
		$newObj->newFromArrayList($source, false);
		return $newObj;

	}

	private function genMealArray(){
		$sourceArray=array(array(
'refId'=>'american',
'name'=>'All American',
'description'=>'Turkey Subs, Chicken Noodle Soup, Hamburgers & Hot Dogs, Chili & Rice and the daily side dishes Fresh Fruit, Raw veggies, Yogurt, Bagels, Granola'
),

array(
'refId'=>'lafiesta',
'name'=>'La Fiesta',
'description'=>'Burritos, Hard or Soft Tacos , Ground Turkey and Rice, Black or Pinto Beans, Home made Salsa, Chicken or Cheese Enchiladas Tortilla Chips and the daily side dishes Fresh Fruit, Raw veggies, Yogurt, Bagels, Granola'
),

array(
'refId'=>'pasta',
'name'=>'Pasta',
'description'=>'Pesto Pasta with Chicken, Spaghetti and Meatballs, Marinara or Meat Sauce, Mac ans Cheese, Homemade Garlic Bread and the daily side dishes Fresh Fruit, Raw veggies, Yogurt, Bagels, Granola'
),

array(
'refId'=>'international',
'name'=>'International',
'description'=>'2 slices of of Good Earth Pizza, Cheese or Sausage, Chicken Rice Bowl, and daily side dishes, Fresh Fruit, Raw Veggies, Yogurt, Bagels, and Granola'
),

array(
'refId'=>'chili',
'name'=>'Chili',
'description'=>'Turkey Chili Nachos, Sloppy Joes, Turkey Chili over Rice, Turkey Chili over Baked Potato'
),

array(
'refId'=>'pizza1c',
'name'=>'Cheese Pizza (one slice)',
'description'=>'1 Slice of Cheese Pizza, Caesar Salad,  Seasonal Veggies,  daily sides, Fresh Fruit, Raw veggies, Yogurt, Bagels, and  Granola'
),

array(
'refId'=>'pizza1s',
'name'=>'Sausage Pizza (one slice)',
'description'=>'1 Slice of Sausage Pizza, Caesar Salad,  Seasonal Veggies,  daily sides, Fresh Fruit, Raw veggies, Yogurt, Bagels, and  Granola'
),

array(
'refId'=>'pizza2c',
'name'=>'Cheese Pizza (two slices)',
'description'=>'2 Slices of Cheese Pizza, Caesar Salad,  Seasonal Veggies,  daily sides, Fresh Fruit, Raw veggies, Yogurt, Bagels, and Granola'
),

array(
'refId'=>'pizza4sc',
'name'=>'Pizza Combo (one each Cheese and Sausage)',
'description'=>'2 Slices of Pizza, 1 Sausage, 1 Cheese, Caesar Salad , daily sides,  Fresh Fruit, Raw veggies, Yogurt, Bagels, and Granola'
),

array(
'refId'=>'chicken',
'name'=>'Chicken',
'description'=>'BBQ, Orange, Teriyaki Chicken and the daily side dishes Fresh Fruit, Raw veggies, Yogurt, Bagels, Granola'
),

);
		$newArray=array();
		foreach ($sourceArray as $data){
			$newArray[]=$data;
		}

		return $newArray;
	}

	public function genOfferingArray(){
	$source=array(	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'5', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'5', 'price'=>'68.40'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'5', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'6', 'price'=>'68.40'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'5', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'7', 'price'=>'68.40'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'5', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'8', 'price'=>'68.40'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'5', 'schoolRefId'=>'33', 'gradeLevelRefId'=>'9', 'price'=>'84.00'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'5', 'schoolRefId'=>'33', 'gradeLevelRefId'=>'10', 'price'=>'84.00'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'5', 'schoolRefId'=>'33', 'gradeLevelRefId'=>'11', 'price'=>'84.00'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'5', 'schoolRefId'=>'33', 'gradeLevelRefId'=>'12', 'price'=>'84.00'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'k', 'price'=>'71.40'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'1', 'price'=>'71.40'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'2', 'price'=>'71.40'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'3', 'price'=>'71.40'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'4', 'price'=>'71.40'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'5', 'price'=>'71.40'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'6', 'price'=>'71.40'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'7', 'price'=>'71.40'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'8', 'price'=>'71.40'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'k', 'price'=>'47.25'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'1', 'price'=>'47.25'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'2', 'price'=>'47.25'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'3', 'price'=>'47.25'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'4', 'price'=>'47.25'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'5', 'price'=>'47.25'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'6', 'price'=>'42.00'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'7', 'price'=>'42.00'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'8', 'price'=>'42.00'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'p', 'price'=>'47.25'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'k', 'price'=>'47.25'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'1', 'price'=>'47.25'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'2', 'price'=>'47.25'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'3', 'price'=>'47.25'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'4', 'price'=>'47.25'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'5', 'price'=>'47.25'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'6', 'price'=>'47.25'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'7', 'price'=>'47.25'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'8', 'price'=>'47.25'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'pk1', 'price'=>'49.50'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'pk2', 'price'=>'49.50'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'pk3', 'price'=>'49.50'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'1', 'price'=>'57.50'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'2', 'price'=>'57.50'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'3', 'price'=>'57.50'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'4', 'price'=>'57.50'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'5', 'price'=>'57.50'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'p', 'price'=>'62.15'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'k', 'price'=>'62.15'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'1', 'price'=>'62.15'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'2', 'price'=>'62.15'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'3', 'price'=>'62.15'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'4', 'price'=>'62.15'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'5', 'price'=>'56.50'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'6', 'price'=>'62.15'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'7', 'price'=>'62.15'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'8', 'price'=>'62.15'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'k', 'price'=>'47.60'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'1', 'price'=>'45.60'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'2', 'price'=>'45.60'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'3', 'price'=>'45.60'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'4', 'price'=>'45.60'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'k', 'price'=>'62.15'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'1', 'price'=>'62.15'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'2', 'price'=>'62.15'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'3', 'price'=>'62.15'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'4', 'price'=>'62.15'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'5', 'price'=>'56.50'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'6', 'price'=>'62.15'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'7', 'price'=>'56.50'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'8', 'price'=>'62.15'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'k', 'price'=>'66.00'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'1', 'price'=>'66.00'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'2', 'price'=>'66.00'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'3', 'price'=>'66.00'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'4', 'price'=>'66.00'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'5', 'price'=>'66.00'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'6', 'price'=>'66.00'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'7', 'price'=>'66.00'),
	array('mealRefId'=>'american', 'name'=>'All American', 'dayRefId'=>'1', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'8', 'price'=>'66.00'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'k', 'price'=>'59.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'1', 'price'=>'59.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'2', 'price'=>'59.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'3', 'price'=>'59.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'4', 'price'=>'59.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'5', 'price'=>'59.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'6', 'price'=>'59.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'7', 'price'=>'59.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'8', 'price'=>'59.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'k', 'price'=>'68.25'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'1', 'price'=>'68.25'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'2', 'price'=>'68.25'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'3', 'price'=>'68.25'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'4', 'price'=>'68.25'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'5', 'price'=>'68.25'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'6', 'price'=>'63.00'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'7', 'price'=>'63.00'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'8', 'price'=>'63.00'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'p', 'price'=>'57.75'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'k', 'price'=>'63.00'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'1', 'price'=>'63.00'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'2', 'price'=>'63.00'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'3', 'price'=>'63.00'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'4', 'price'=>'63.00'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'5', 'price'=>'63.00'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'6', 'price'=>'63.00'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'7', 'price'=>'63.00'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'8', 'price'=>'63.00'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'pk1', 'price'=>'54.45'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'pk2', 'price'=>'54.45'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'pk3', 'price'=>'54.45'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'1', 'price'=>'63.25'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'2', 'price'=>'63.25'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'3', 'price'=>'63.25'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'4', 'price'=>'63.25'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'5', 'price'=>'63.25'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'p', 'price'=>'56.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'k', 'price'=>'56.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'1', 'price'=>'56.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'2', 'price'=>'56.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'3', 'price'=>'56.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'4', 'price'=>'56.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'5', 'price'=>'50.85'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'6', 'price'=>'56.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'7', 'price'=>'50.85'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'8', 'price'=>'50.85'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'k', 'price'=>'71.40'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'1', 'price'=>'68.40'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'2', 'price'=>'68.40'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'3', 'price'=>'68.40'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'4', 'price'=>'68.40'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'k', 'price'=>'56.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'1', 'price'=>'56.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'2', 'price'=>'62.15'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'3', 'price'=>'62.15'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'4', 'price'=>'62.15'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'5', 'price'=>'56.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'6', 'price'=>'56.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'7', 'price'=>'56.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'8', 'price'=>'62.15'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'k', 'price'=>'60.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'1', 'price'=>'60.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'2', 'price'=>'60.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'3', 'price'=>'60.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'4', 'price'=>'60.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'5', 'price'=>'60.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'6', 'price'=>'60.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'7', 'price'=>'60.50'),
	array('mealRefId'=>'chicken', 'name'=>'Chicken', 'dayRefId'=>'5', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'8', 'price'=>'60.50'),
	array('mealRefId'=>'chili', 'name'=>'Chili', 'dayRefId'=>'1', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'5', 'price'=>'45.60'),
	array('mealRefId'=>'chili', 'name'=>'Chili', 'dayRefId'=>'1', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'6', 'price'=>'45.60'),
	array('mealRefId'=>'chili', 'name'=>'Chili', 'dayRefId'=>'1', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'7', 'price'=>'45.60'),
	array('mealRefId'=>'chili', 'name'=>'Chili', 'dayRefId'=>'1', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'8', 'price'=>'45.60'),
	array('mealRefId'=>'international', 'name'=>'International   2 Slices of Good Earth Pizza, Cheese or Turkey Sausage, Chicken Rice Bowl', 'dayRefId'=>'3', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'pk1', 'price'=>'59.40'),
	array('mealRefId'=>'international', 'name'=>'International   2 Slices of Good Earth Pizza, Cheese or Turkey Sausage, Chicken Rice Bowl', 'dayRefId'=>'3', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'pk2', 'price'=>'59.40'),
	array('mealRefId'=>'international', 'name'=>'International   2 Slices of Good Earth Pizza, Cheese or Turkey Sausage, Chicken Rice Bowl', 'dayRefId'=>'3', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'pk3', 'price'=>'59.40'),
	array('mealRefId'=>'international', 'name'=>'International   2 Slices of Good Earth Pizza, Cheese or Turkey Sausage, Chicken Rice Bowl', 'dayRefId'=>'3', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'1', 'price'=>'69.00'),
	array('mealRefId'=>'international', 'name'=>'International   2 Slices of Good Earth Pizza, Cheese or Turkey Sausage, Chicken Rice Bowl', 'dayRefId'=>'3', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'2', 'price'=>'69.00'),
	array('mealRefId'=>'international', 'name'=>'International   2 Slices of Good Earth Pizza, Cheese or Turkey Sausage, Chicken Rice Bowl', 'dayRefId'=>'3', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'3', 'price'=>'69.00'),
	array('mealRefId'=>'international', 'name'=>'International   2 Slices of Good Earth Pizza, Cheese or Turkey Sausage, Chicken Rice Bowl', 'dayRefId'=>'3', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'4', 'price'=>'69.00'),
	array('mealRefId'=>'international', 'name'=>'International   2 Slices of Good Earth Pizza, Cheese or Turkey Sausage, Chicken Rice Bowl', 'dayRefId'=>'3', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'5', 'price'=>'69.00'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'k', 'price'=>'89.25'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'1', 'price'=>'89.25'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'2', 'price'=>'89.25'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'3', 'price'=>'89.25'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'4', 'price'=>'89.25'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'5', 'price'=>'89.25'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'6', 'price'=>'89.25'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'7', 'price'=>'89.25'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'8', 'price'=>'89.25'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'k', 'price'=>'63.00'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'1', 'price'=>'63.00'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'2', 'price'=>'63.00'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'3', 'price'=>'63.00'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'4', 'price'=>'63.00'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'5', 'price'=>'63.00'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'6', 'price'=>'57.75'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'7', 'price'=>'57.75'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'8', 'price'=>'57.75'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'p', 'price'=>'57.75'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'k', 'price'=>'57.75'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'1', 'price'=>'57.75'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'2', 'price'=>'57.75'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'3', 'price'=>'57.75'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'4', 'price'=>'57.75'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'5', 'price'=>'57.75'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'6', 'price'=>'57.75'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'7', 'price'=>'57.75'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'8', 'price'=>'57.75'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'5', 'price'=>'68.40'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'6', 'price'=>'68.40'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'7', 'price'=>'68.40'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'8', 'price'=>'68.40'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'pk1', 'price'=>'54.45'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'pk2', 'price'=>'54.45'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'pk3', 'price'=>'54.45'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'1', 'price'=>'63.25'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'2', 'price'=>'63.25'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'3', 'price'=>'63.25'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'4', 'price'=>'63.25'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'5', 'price'=>'63.25'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'p', 'price'=>'67.80'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'k', 'price'=>'67.80'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'1', 'price'=>'67.80'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'2', 'price'=>'67.80'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'3', 'price'=>'67.80'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'4', 'price'=>'67.80'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'5', 'price'=>'62.15'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'6', 'price'=>'67.80'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'7', 'price'=>'62.15'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'8', 'price'=>'62.15'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'k', 'price'=>'65.45'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'1', 'price'=>'62.70'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'2', 'price'=>'62.70'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'3', 'price'=>'62.70'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'4', 'price'=>'62.70'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'k', 'price'=>'73.45'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'1', 'price'=>'73.45'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'2', 'price'=>'73.45'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'3', 'price'=>'73.45'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'4', 'price'=>'73.45'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'5', 'price'=>'73.45'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'6', 'price'=>'67.80'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'7', 'price'=>'67.80'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'8', 'price'=>'73.45'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'33', 'gradeLevelRefId'=>'9', 'price'=>'98.00'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'33', 'gradeLevelRefId'=>'10', 'price'=>'98.00'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'33', 'gradeLevelRefId'=>'11', 'price'=>'98.00'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'33', 'gradeLevelRefId'=>'12', 'price'=>'98.00'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'k', 'price'=>'71.50'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'1', 'price'=>'71.50'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'2', 'price'=>'71.50'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'3', 'price'=>'71.50'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'4', 'price'=>'71.50'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'5', 'price'=>'71.50'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'6', 'price'=>'71.50'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'7', 'price'=>'71.50'),
	array('mealRefId'=>'lafiesta', 'name'=>'La Fiesta', 'dayRefId'=>'2', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'8', 'price'=>'71.50'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'k', 'price'=>'71.40'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'1', 'price'=>'71.40'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'2', 'price'=>'71.40'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'3', 'price'=>'71.40'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'4', 'price'=>'71.40'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'5', 'price'=>'71.40'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'6', 'price'=>'71.40'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'7', 'price'=>'71.40'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'8', 'price'=>'71.40'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'k', 'price'=>'52.50'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'1', 'price'=>'52.50'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'2', 'price'=>'52.50'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'3', 'price'=>'52.50'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'4', 'price'=>'52.50'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'5', 'price'=>'52.50'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'6', 'price'=>'47.25'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'7', 'price'=>'47.25'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'8', 'price'=>'47.25'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'p', 'price'=>'57.75'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'k', 'price'=>'63.00'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'1', 'price'=>'63.00'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'2', 'price'=>'63.00'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'3', 'price'=>'63.00'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'4', 'price'=>'63.00'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'5', 'price'=>'63.00'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'6', 'price'=>'63.00'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'7', 'price'=>'63.00'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'8', 'price'=>'63.00'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'5', 'price'=>'68.40'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'6', 'price'=>'68.40'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'7', 'price'=>'68.40'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'8', 'price'=>'68.40'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'pk1', 'price'=>'54.45'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'pk2', 'price'=>'54.45'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'pk3', 'price'=>'54.45'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'1', 'price'=>'63.25'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'2', 'price'=>'63.25'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'3', 'price'=>'63.25'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'4', 'price'=>'63.25'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'21', 'gradeLevelRefId'=>'5', 'price'=>'63.25'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'p', 'price'=>'67.80'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'k', 'price'=>'67.80'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'1', 'price'=>'67.80'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'2', 'price'=>'67.80'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'3', 'price'=>'67.80'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'4', 'price'=>'67.80'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'5', 'price'=>'62.15'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'6', 'price'=>'67.80'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'7', 'price'=>'62.15'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'8', 'price'=>'56.50'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'k', 'price'=>'71.40'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'1', 'price'=>'68.40'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'2', 'price'=>'68.40'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'3', 'price'=>'68.40'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'4', 'price'=>'68.40'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'k', 'price'=>'62.15'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'1', 'price'=>'62.15'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'2', 'price'=>'67.80'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'3', 'price'=>'67.80'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'4', 'price'=>'67.80'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'5', 'price'=>'62.15'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'6', 'price'=>'62.15'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'7', 'price'=>'62.15'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'8', 'price'=>'62.15'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'k', 'price'=>'60.50'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'1', 'price'=>'60.50'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'2', 'price'=>'60.50'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'3', 'price'=>'60.50'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'4', 'price'=>'60.50'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'5', 'price'=>'60.50'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'6', 'price'=>'60.50'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'7', 'price'=>'60.50'),
	array('mealRefId'=>'pasta', 'name'=>'pasta', 'dayRefId'=>'4', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'8', 'price'=>'60.50'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'k', 'price'=>'71.40'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'1', 'price'=>'71.40'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'2', 'price'=>'71.40'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'3', 'price'=>'71.40'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'4', 'price'=>'71.40'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'5', 'price'=>'71.40'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'6', 'price'=>'71.40'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'7', 'price'=>'71.40'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'9', 'gradeLevelRefId'=>'8', 'price'=>'71.40'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'k', 'price'=>'63.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'1', 'price'=>'63.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'2', 'price'=>'63.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'3', 'price'=>'63.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'4', 'price'=>'63.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'5', 'price'=>'63.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'6', 'price'=>'57.75'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'7', 'price'=>'57.75'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'34', 'gradeLevelRefId'=>'8', 'price'=>'57.75'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'p', 'price'=>'57.75'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'k', 'price'=>'57.75'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'1', 'price'=>'63.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'2', 'price'=>'63.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'3', 'price'=>'63.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'4', 'price'=>'63.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'5', 'price'=>'63.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'6', 'price'=>'63.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'7', 'price'=>'63.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'5', 'gradeLevelRefId'=>'8', 'price'=>'63.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'p', 'price'=>'56.50'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'k', 'price'=>'56.50'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'1', 'price'=>'56.50'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'2', 'price'=>'56.50'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'3', 'price'=>'56.50'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'4', 'price'=>'56.50'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'5', 'price'=>'50.85'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'6', 'price'=>'56.50'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'7', 'price'=>'50.85'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'8', 'gradeLevelRefId'=>'8', 'price'=>'50.85'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'k', 'price'=>'62.15'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'1', 'price'=>'62.15'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'2', 'price'=>'67.80'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'3', 'price'=>'67.80'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'4', 'price'=>'67.80'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'5', 'price'=>'62.15'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'6', 'price'=>'62.15'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'7', 'price'=>'62.15'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'3', 'gradeLevelRefId'=>'8', 'price'=>'67.80'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'k', 'price'=>'55.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'1', 'price'=>'55.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'2', 'price'=>'55.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'3', 'price'=>'55.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'4', 'price'=>'55.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'5', 'price'=>'55.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'6', 'price'=>'55.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'7', 'price'=>'55.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'28', 'gradeLevelRefId'=>'8', 'price'=>'55.00'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'5', 'price'=>'68.40'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'6', 'price'=>'68.40'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'7', 'price'=>'68.40'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'8', 'price'=>'68.40'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'k', 'price'=>'71.40'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'1', 'price'=>'68.40'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'2', 'price'=>'68.40'),
	array('mealRefId'=>'pizza1c', 'name'=>'Cheese Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'3', 'price'=>'68.40'),
	array('mealRefId'=>'pizza1s', 'name'=>'Sausage Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'4', 'price'=>'68.40'),
	array('mealRefId'=>'pizza1s', 'name'=>'Sausage Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'5', 'price'=>'70.80'),
	array('mealRefId'=>'pizza1s', 'name'=>'Sausage Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'6', 'price'=>'70.80'),
	array('mealRefId'=>'pizza1s', 'name'=>'Sausage Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'7', 'price'=>'70.80'),
	array('mealRefId'=>'pizza1s', 'name'=>'Sausage Pizza (one slice)', 'dayRefId'=>'3', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'8', 'price'=>'70.80'),
	array('mealRefId'=>'pizza2c', 'name'=>'Cheese Pizza (two slices)', 'dayRefId'=>'3', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'5', 'price'=>'85.20'),
	array('mealRefId'=>'pizza2c', 'name'=>'Cheese Pizza (two slices)', 'dayRefId'=>'3', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'6', 'price'=>'85.20'),
	array('mealRefId'=>'pizza2c', 'name'=>'Cheese Pizza (two slices)', 'dayRefId'=>'3', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'7', 'price'=>'85.20'),
	array('mealRefId'=>'pizza2c', 'name'=>'Cheese Pizza (two slices)', 'dayRefId'=>'3', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'8', 'price'=>'85.20'),
	array('mealRefId'=>'pizza2c', 'name'=>'Cheese Pizza (two slices)', 'dayRefId'=>'3', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'k', 'price'=>'88.20'),
	array('mealRefId'=>'pizza2c', 'name'=>'Cheese Pizza (two slices)', 'dayRefId'=>'3', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'1', 'price'=>'85.20'),
	array('mealRefId'=>'pizza2c', 'name'=>'Cheese Pizza (two slices)', 'dayRefId'=>'3', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'2', 'price'=>'85.20'),
	array('mealRefId'=>'pizza2c', 'name'=>'Cheese Pizza (two slices)', 'dayRefId'=>'3', 'schoolRefId'=>'22', 'gradeLevelRefId'=>'3', 'price'=>'85.20'),
	array('mealRefId'=>'pizza4sc', 'name'=>'Pizza Combo (one each Cheese and Sausage)', 'dayRefId'=>'3', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'5', 'price'=>'87.60'),
	array('mealRefId'=>'pizza4sc', 'name'=>'Pizza Combo (one each Cheese and Sausage)', 'dayRefId'=>'3', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'6', 'price'=>'87.60'),
	array('mealRefId'=>'pizza4sc', 'name'=>'Pizza Combo (one each Cheese and Sausage)', 'dayRefId'=>'3', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'7', 'price'=>'87.60'),
	array('mealRefId'=>'pizza4sc', 'name'=>'Pizza Combo (one each Cheese and Sausage)', 'dayRefId'=>'3', 'schoolRefId'=>'29', 'gradeLevelRefId'=>'8', 'price'=>'87.60')
	);

		return $source;
	}

	public function getSchoolGradeLevelArray(){
	return array(
		array('schoolRefId'=>'9', 'gradeLevelRefId'=>'k'),
		array('schoolRefId'=>'9', 'gradeLevelRefId'=>'1'),
		array('schoolRefId'=>'9', 'gradeLevelRefId'=>'2'),
		array('schoolRefId'=>'9', 'gradeLevelRefId'=>'3'),
		array('schoolRefId'=>'9', 'gradeLevelRefId'=>'4'),
		array('schoolRefId'=>'9', 'gradeLevelRefId'=>'5'),
		array('schoolRefId'=>'9', 'gradeLevelRefId'=>'6'),
		array('schoolRefId'=>'9', 'gradeLevelRefId'=>'7'),
		array('schoolRefId'=>'9', 'gradeLevelRefId'=>'8'),
		array('schoolRefId'=>'', 'gradeLevelRefId'=>''),
		array('schoolRefId'=>'34', 'gradeLevelRefId'=>'k'),
		array('schoolRefId'=>'34', 'gradeLevelRefId'=>'1'),
		array('schoolRefId'=>'34', 'gradeLevelRefId'=>'2'),
		array('schoolRefId'=>'34', 'gradeLevelRefId'=>'3'),
		array('schoolRefId'=>'34', 'gradeLevelRefId'=>'4'),
		array('schoolRefId'=>'34', 'gradeLevelRefId'=>'5'),
		array('schoolRefId'=>'34', 'gradeLevelRefId'=>'6'),
		array('schoolRefId'=>'34', 'gradeLevelRefId'=>'7'),
		array('schoolRefId'=>'34', 'gradeLevelRefId'=>'8'),
		array('schoolRefId'=>'', 'gradeLevelRefId'=>''),
		array('schoolRefId'=>'5', 'gradeLevelRefId'=>'p'),
		array('schoolRefId'=>'5', 'gradeLevelRefId'=>'k'),
		array('schoolRefId'=>'5', 'gradeLevelRefId'=>'1'),
		array('schoolRefId'=>'5', 'gradeLevelRefId'=>'2'),
		array('schoolRefId'=>'5', 'gradeLevelRefId'=>'3'),
		array('schoolRefId'=>'5', 'gradeLevelRefId'=>'4'),
		array('schoolRefId'=>'5', 'gradeLevelRefId'=>'5'),
		array('schoolRefId'=>'5', 'gradeLevelRefId'=>'6'),
		array('schoolRefId'=>'5', 'gradeLevelRefId'=>'7'),
		array('schoolRefId'=>'5', 'gradeLevelRefId'=>'8'),
		array('schoolRefId'=>'', 'gradeLevelRefId'=>''),
		array('schoolRefId'=>'29', 'gradeLevelRefId'=>'5'),
		array('schoolRefId'=>'29', 'gradeLevelRefId'=>'6'),
		array('schoolRefId'=>'29', 'gradeLevelRefId'=>'7'),
		array('schoolRefId'=>'29', 'gradeLevelRefId'=>'8'),
		array('schoolRefId'=>'', 'gradeLevelRefId'=>''),
		array('schoolRefId'=>'21', 'gradeLevelRefId'=>'pk1'),
		array('schoolRefId'=>'21', 'gradeLevelRefId'=>'pk2'),
		array('schoolRefId'=>'21', 'gradeLevelRefId'=>'pk3'),
		array('schoolRefId'=>'21', 'gradeLevelRefId'=>'1'),
		array('schoolRefId'=>'21', 'gradeLevelRefId'=>'2'),
		array('schoolRefId'=>'21', 'gradeLevelRefId'=>'3'),
		array('schoolRefId'=>'21', 'gradeLevelRefId'=>'4'),
		array('schoolRefId'=>'21', 'gradeLevelRefId'=>'5'),
		array('schoolRefId'=>'', 'gradeLevelRefId'=>''),
		array('schoolRefId'=>'8', 'gradeLevelRefId'=>'p'),
		array('schoolRefId'=>'8', 'gradeLevelRefId'=>'k'),
		array('schoolRefId'=>'8', 'gradeLevelRefId'=>'1'),
		array('schoolRefId'=>'8', 'gradeLevelRefId'=>'2'),
		array('schoolRefId'=>'8', 'gradeLevelRefId'=>'3'),
		array('schoolRefId'=>'8', 'gradeLevelRefId'=>'4'),
		array('schoolRefId'=>'8', 'gradeLevelRefId'=>'5'),
		array('schoolRefId'=>'8', 'gradeLevelRefId'=>'6'),
		array('schoolRefId'=>'8', 'gradeLevelRefId'=>'7'),
		array('schoolRefId'=>'8', 'gradeLevelRefId'=>'8'),
		array('schoolRefId'=>'22', 'gradeLevelRefId'=>'k'),
		array('schoolRefId'=>'22', 'gradeLevelRefId'=>'1'),
		array('schoolRefId'=>'22', 'gradeLevelRefId'=>'2'),
		array('schoolRefId'=>'22', 'gradeLevelRefId'=>'3'),
		array('schoolRefId'=>'22', 'gradeLevelRefId'=>'4'),
		array('schoolRefId'=>'3', 'gradeLevelRefId'=>'k'),
		array('schoolRefId'=>'3', 'gradeLevelRefId'=>'1'),
		array('schoolRefId'=>'3', 'gradeLevelRefId'=>'2'),
		array('schoolRefId'=>'3', 'gradeLevelRefId'=>'3'),
		array('schoolRefId'=>'3', 'gradeLevelRefId'=>'4'),
		array('schoolRefId'=>'3', 'gradeLevelRefId'=>'5'),
		array('schoolRefId'=>'3', 'gradeLevelRefId'=>'6'),
		array('schoolRefId'=>'3', 'gradeLevelRefId'=>'7'),
		array('schoolRefId'=>'3', 'gradeLevelRefId'=>'8'),
		array('schoolRefId'=>'33', 'gradeLevelRefId'=>'9'),
		array('schoolRefId'=>'33', 'gradeLevelRefId'=>'10'),
		array('schoolRefId'=>'33', 'gradeLevelRefId'=>'11'),
		array('schoolRefId'=>'33', 'gradeLevelRefId'=>'12'),
		array('schoolRefId'=>'28', 'gradeLevelRefId'=>'k'),
		array('schoolRefId'=>'28', 'gradeLevelRefId'=>'1'),
		array('schoolRefId'=>'28', 'gradeLevelRefId'=>'2'),
		array('schoolRefId'=>'28', 'gradeLevelRefId'=>'3'),
		array('schoolRefId'=>'28', 'gradeLevelRefId'=>'4'),
		array('schoolRefId'=>'28', 'gradeLevelRefId'=>'5'),
		array('schoolRefId'=>'28', 'gradeLevelRefId'=>'6'),
		array('schoolRefId'=>'28', 'gradeLevelRefId'=>'7'),
		array('schoolRefId'=>'28', 'gradeLevelRefId'=>'8')
		);
}

}









