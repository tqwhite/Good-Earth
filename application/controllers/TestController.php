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
/*
		$mealArray=$this->genMealArray();
		$offeringArray=$this->genOfferingArray(3);

			$mealObj=new \Application_Model_Meal();
			$mealEntityList=$mealObj->newFromArrayList($mealArray);

		$offeringObj=new \Application_Model_Offering();
		$offeringEntityList=$offeringObj->newFromArrayList($offeringArray);
*/

		$offeringObj=new \Application_Model_Account();
echo "hello"."<br/>";
		$result=$offeringObj->getByRefId('500379d605f7a');
		print_r(\Application_Model_Account::formatOutput($result));
		echo 'g'.$result->offeringGradeLevelNodes."<br/>";
exit;
/*
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
		$offeringEntityList=$offeringObj->newFromArrayList($offeringSourceArrayList);
		$offeringEntity=$offeringEntityList[0]; //newFromArrayList() produces an array, even if only one
 echo 'Offering='.$offeringEntity->name."<br/>";

 		$this->em->flush();
*/
		$serverComm=array();
			$serverComm[]=array("fieldName"=>"user_confirm_message", "value"=>'test complete');
			$serverComm[]=array("fieldName"=>"assert_initial_controller", "value"=>'none');

		$this->view->serverComm=$this->_helper->WriteServerCommDiv($serverComm); //named: Q_Controller_Action_Helper_WriteServerCommDiv
	}

    public function offeringsDevelopmentMethod()
    {

		$mealSourceArrayList=array(array(
			name=>'Pizza Galore2',
			shortName=>'test shortName',
			description=>'test description',
			suggestedPrice=>222
		));
		$mealObj=new \Application_Model_Meal();
		$mealEntityList=$mealObj->newFromArrayList($mealSourceArrayList);
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
		$offeringEntityList=$offeringObj->newFromArrayList($offeringSourceArrayList);
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
			$this->view->message .= "-----gradeLevelSchools were initialized<p/>";



			$this->view->message .= "initializing meals and offerings test data<br/>";
		$mealArray=$this->genMealArray();
		$offeringArray=$this->genOfferingArray(8);

			$mealObj=new \Application_Model_Meal();
			$mealEntityList=$mealObj->newFromArrayList($mealArray);
			$this->view->message .= "-----created ".count($mealEntityList)." meals<br/>";

		$offeringObj=new \Application_Model_Offering();
		$offeringEntityList=$offeringObj->newFromArrayList($offeringArray);
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
		array('name'=>'Saint Marks', refId=>'SaintMarks'),
		array('name'=>'Lycee Français', refId=>'LyceeFrancais'),
		array('name'=>'Neil Cummins', refId=>'NeilCummins'),
		array('name'=>'Cascade Canyon', refId=>'CascadeCanyon'),
		array('name'=>'Marin Horizon', refId=>'MarinHorizon'),
		array('name'=>'St Anselm', refId=>'StAnselm'),
		array('name'=>'Hall Middle School', refId=>'HallMiddleSchool'),
		array('name'=>'Sonoma Academy', refId=>'SonomaAcademy'),
		array('name'=>'Good Shepherd', refId=>'GoodShepherd'),
		array('name'=>'Marin Christian Academy', refId=>'MarinChristianAcademy')
	);

	$newObj=new \Application_Model_School();
	$newObj->newFromArrayList($source);
	return $newObj;

    }

	private function _initGradesLevels(){
		$source=array(
			array('title'=>'Preschool', refId=>'Preschool'),
			array('title'=>'Kindergarten', refId=>'Kindergarten'),
			array('title'=>'First', refId=>'First'),
			array('title'=>'Second', refId=>'Second'),
			array('title'=>'Third', refId=>'Third'),
			array('title'=>'Fourth', refId=>'Fourth'),
			array('title'=>'Fifth', refId=>'Fifth'),
			array('title'=>'Sixth', refId=>'Sixth'),
			array('title'=>'Seventh', refId=>'Seventh'),
			array('title'=>'Eight', refId=>'Eight')
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
			array(title=>'Mon', refId=>'1'),
			array(title=>'Tues', refId=>'2'),
			array(title=>'Weds', refId=>'3'),
			array(title=>'Thurs', refId=>'4'),
			array(title=>'Fri', refId=>'5')
		);

		$newObj=new \Application_Model_Day();
		$newObj->newFromArrayList($source);
		return $newObj;

	}

	private function genMealArray(){
		$sourceArray=array(array(refId=>"742ff41d83439a60b0ccbb11a80838cc", description=>"Turkey sub with Chicken Noodle Soup, Hamburgers and Hot Dogs/Chicken with Rice Soup, Hamburgers/ Hot Dogs with Potato Wedges, Chicken Rice Bowl/Orange or Teriyaki Sauce "),
		array(refId=>"cd07909845cc381e776b18f3ffcbcb8d", description=>"Burrito, Hard or Soft Taco Bar with Rice and Beans, All the Toppings, *Cheese or Chicken Enchiladas, Build your own Nachos with Blue Chips, Homemade Salsa"),
		array(refId=>"c67f7d1f6557cd3e0f386343dacd6e0a", description=>"Pesto Pasta with Chicken, Pasta with Meat Balls, Marinara, Meat Sauce or Pesto, Meat, or Pesto Sauce, Homemade Garlic Bread"),
		array(refId=>"3a51c2b45ef0ef24a883cceb35ccfcef", description=>"Teriyaki Chicken with Rice and Broccoli, BBQ Chicken with Potatoes and Corn, Orange Chicken with Rice and Broccoli "),
		array(refId=>"afce52bccc1d9e83b416fd9259be438f", description=>"Teriyaki Chicken with Rice and Broccoli, BBQ Chicken with Potatoes and Corn, Orange Chicken with Rice and Broccoli, "),
		array(refId=>"36552b067851d0e8f3b038e565d53d38", description=>"BBQ Chicken with Potatoes and Corn, Teriyaki Chicken with Rice and Broccoli"),
		array(refId=>"464d19275016ca42fbee60daa7956ba9", description=>"Burrito, Corn or Flour Tortillas, with Ground Turkey, Choice of Black or Pinto Beans, with Homemade Salsa, Build your own Nachos with Blue Chips, Cheese, Tomatoes. "),
		array(refId=>"e43da734dfb2f1f360408c522bba7e2a", description=>"Back by popular demand...Extreme Pizzeria! 2 slices of Cheese or Pepperoni pizza, Plus, the Good Earth Caesar Salad, Cut Veggies and Fruit"),
		array(refId=>"18615b76858d66cc3914abe0a9ade8d6", description=>"Pesto Pasta with Chicken, Pasta with Meat Balls, Mar"),
		array(refId=>"6748b2a5d496eb4a83aac66602b1772d", description=>"Turkey sub-sandwiches with optional cheese, lettuce, tomato, onions and pickles And Homemade Chicken Soup or Vegetable Soup "),
		array(refId=>"0c43205292c58d65bc96d9fc14d80bd7", description=>"Burritos/Tacos with Choice of Turkey, Rice, Bean & Cheese Fillings,Chicken or Cheese Enchiladas or Rice, Bean and Cheese Burrito, Optional Sour Cream and Homemade Salsa Each Day"),
		array(refId=>"4949c0eb8699dc03231bc59b369005f0", description=>"One slice Good Earth Sausage Pizza"),
		array(refId=>"b7ac878b02422515905af27851334f9c", description=>"Turkey chili Nachos, Sloppy Joes w/Rice, Turkey Chili w/Baked Potatoes, Sour Cream, Butter."),
		array(refId=>"b3e07057380c00a5ae13b0218e2c9bc8", description=>"Sliced Chicken Breast in a delicious Teriyaki Sauce, Plus, Jasmine Rice and Steamed Broccoli, BBQ Chicken"),
		array(refId=>"a1fd1358044cde24df2cc6484d124c43", description=>"Turkey Sandwiches with Chicken Noodle Soup/Chicken with Rice Soup"),
		array(refId=>"6c3d207c4d65c8564cf35769cbe9e268", description=>"Choice of Hamburgers, Hot dogs, Optional Chili (on hot dogs or as a side dish) & Daily Sides "),
		array(refId=>"1db059f066b8924112a4765fed18e593", description=>"Pasta with your choice of a Marinara Sauce, Meat Sauce or Pesto, Also Plain with Parmesan upon request, Plus, Homemade Garlic Bread"),
		array(refId=>"64f62871f7846fd2671626ef94277db4", description=>"\'Build Your Own\' Burrito or Hard Shell Taco"),
		array(refId=>"67d24f0ad01a2b994870ee0615522aaa", description=>"NEW ENTRE OPTION! Chicken Caesar Salad w/Garlic Bread, Caesar Salad, Sliced Apples, Fresh Cut Veggies."),
		array(refId=>"096d683e2227881c9ea5161dba3f96c3", description=>"Turkey Sub-Sanwiches with optional cheese, lettuce, tomato, onions and pickles and Homemade Soup or *Veggie Dog"),
		array(refId=>"63ace2853e205f69a8e6619fb6b3cab5", description=>"Hamburgers and Hot Dogs, Optional Chili (on hot dogs or as a side dish) + Daily Sides"),
		array(refId=>"57999a27ce64e351e58568c55699820f", description=>"BBQ Chicken Pieces, Teriyaki Chicken, Orange Chicken."),
		array(refId=>"71b7a2be1a71bafa4867a1d29c47b6b3", description=>"Soft or Hard Tacos with Choice of Rice, Bean, Cheese and Meat Fillings, Optional sour cream and homemade salsa each day, & Daily sides"),
		array(refId=>"1324a08767ed2d761b5c4835379e3eea", description=>" Turkey Subs with Chicken Noodle Soup/Chicken with Rice Soup, Chicken Rice Bowl with Orange/Teriyaki Sauce"),
		array(refId=>"870213e1f427bc7eacd7de0d26dae62b", description=>"One slice Good Earth Cheese Pizza"),
		array(refId=>"0fb037684f3707e123f1ff4bc65a1b03", description=>"Turkey sub-sandwiches with optional cheese, lettuce, tomato, onions and pickles, Homemade Soup or *Veggie Dog"),
		array(refId=>"843f7b553a956d935dbbe13ff0d7b6fe", description=>"hamburgers and Hot Dogs with Potato Wedges, Turkey Subs with Chicken Noodle Soup/Chicken with Rice Soup, Chicken Rice Bowl with Orange/Teriyaki Sauce"),
		array(refId=>"750939246d16af896952b489dd2ac800", description=>"Pesto Pasta with Chicken, Pasta with Meat Balls, Marinara, Meat, or Pesto Sauce, Homemade Garlic Bread"),
		array(refId=>"12b2a450dfac511929ec3012f1bbbc43", description=>"Hamburgers and Hot Dogs with Potato Wedges, Turkey Subs with Chicken Noodle Soup/Chicken with Rice Soup, Chicken Rice Bowl with Orange/Teriyaki Sauce"),
		array(refId=>"775d9c5afa56302e2d65932eb14afd81", description=>"Two slices both cheese Good Earth Pizza"),
		array(refId=>"581e29709d357618d809af4e664d687a", description=>"\Build Your Own\" Turkey Sub-sandwiches"),
		array(refId=>"4198b689f5d1b04538e4d7b9d9abb64b", description=>"Burritos, Hard or Soft Tacos, Flour or Corn Tortillas, with Ground Turkey, Choice of Black or Pinto Beans, Build your own Nachos, with Blue Chips, tomatoes, cheese, Homemade Salsa, Sour Cream, Chicken or Cheese Enchiladas"),
		array(refId=>"6d829f9c63d42d30755befa6a84f30f5", description=>"2 Slices of Good Earth Pizza: Cheese, Turkey Sausage, (Gluten Free Option available)"),
		array(refId=>"2c42968d4f7413a9de1277c72d1992e5", description=>"2 Slices of Good Earth Pizza: Veggie, Cheese, Turkey Sausage and Pineapple, (Gluten Free Option available- Rice & Tapioca Flour)"),
		array(refId=>"907c68b3575955cea3d17709c965b095", description=>"Hot Dogs or Hamburgers with Chicken Noodle Soup, Turkey Subs"),
		array(refId=>"4f06c6206a4f9ee658edf402e361486c", description=>"Burrito, Hard or Soft Taco Bar with Rice and Beans, All of the Toppings, *Cheese or Chicken Enchiladas "),
		array(refId=>"6753e7275485ecb79f86a974afc9a772", description=>"2 Slices of Good Earth Pizza: Cheese, Turkey Sausage. (Gluten Free Option available- Rice & Tapioca Flour)"),
		array(refId=>"7576643483abb0e80493f635ebff8cc9", description=>"2 Slices of Good Earth Pizza: Cheese, Turkey Sausage and Pineapple. (Gluten Free Option available- Rice & Tapioca Flour)"),
		array(refId=>"4926362df7f37cc92ccecdd48c321ff5", description=>"Two slices one cheese, one sausage Good Earth Pizza"),
		array(refId=>"3b5c67b4ad05a0a327ed4a77b212b6be", description=>"NEW ENTREE OPTION! Chicken Caesar Salad with Garlic Bread, Caesar Salad, Sliced Apples, Fresh Cut Veggies."),
		array(refId=>"8889e2caf0112809967f5e2f4cc2a830", description=>"2 Slices of Good Earth Pizza, Cheese, Turkey Sausage and Pineapple. (Gluten Free Option available- Rice & Tapioca Flour)"),
		array(refId=>"ede16b0aaac42bb2e02be85a1efe7e01", description=>"Spaghetti with Marinara, Meat, or Pesto Sauce, Macaroni & Cheese or Plain Pasta, Homemade Garlic Bread"),
		array(refId=>"07d935680b6501b2e42fe4baea021389", description=>"mk"),
		array(refId=>"fbb5a98089295bb42aea3591e01892e8", description=>"Burrito, Hard or Soft Tacos"),
		array(refId=>"1cc4c77e03188408eb2b42beda97b5cb", description=>"Chicken Caesar Salad w/Garlic Bread, Caesar Salad, Sliced Apples, Fresh Cut Veggies"),
		array(refId=>"b8f2046cf5666c2b59a4b431269c3300", description=>"BBQ Chicken with Potatoes and Corn, Teriyaki Chicken with Rice and Broccoli, Orange Chicken"),
		array(refId=>"072458bc319697bde5e65f08c1bd67c9", description=>"Spaghetti and Meatballs, Penne Pasta with choice of Marinara, Meat, or Pesto Sauce, homemade garlic bread"),
		array(refId=>"230e80e09efd7138766a4efb98e8789c", description=>"Spaghetti with Marinara, Meat, or Pesto Sauce, Macaroni & Cheese or Plain Pasta, Pesto Pasta with chicken Homemade Garlic Bread"),
		array(refId=>"2312179f558625307a956f622f96eb51", description=>"Choice of Ground Turkey, Rice, Choice of Black or Pinto Beans, Burritos, Hard or Soft Tacos,/Flour or Corn Tortillas, Build your own Nachos, with Blue Chips, Cheese, Sour Cream, Pico Sauce, Homemade Salsa."),
		array(refId=>"fd414d6a3bb3d3fc25a78bf2872a96d6", description=>"Pesto Pasta with Chicken, BBQ Chicken, Teriyaki Chicken, Orange Chicken, Mac and Cheese"),
		array(refId=>"94d070607e8d374d9ff32d4a596e82ee", description=>"Baked Pasta of the Day, Pesto Chicken Pasta, Macaroni & Cheese, Spaghetti & Meat Sauce, or Spaghetti & Meatballs, Optional every week:Plain Pasta with Parmesan or Pasta, Homemade Garlic Bread, & Daily Sides"),
		array(refId=>"003d0b96a4bf96a05286f57fd00857a8", description=>"Pasta with Marinara, Meat Sauce, Parmesan Cheese or Pesto or Pasta of the Day (One of the follwing pasta entrees will be offered weekly: pesto chicken, turkey tomato cream, macaroni & cheese or spaghetti and meatballs) Homemade Garlic Bread"),
		array(refId=>"e164298e37fbb31e433f4bced534fcab", description=>"Turkey sub with Chicken Noodle Soup, Hamburgers, Hot Dogs with Chili and Rice"),
		array(refId=>"225bcb0d754747f4fc743ccaf084d008", description=>"2 Slices of Good Earth Pizza, Cheese, Turkey Sausage, (Gluten Free Option available-rice and tapioca flour)"),
		array(refId=>"6dd297533ceceb166304c502bcd1b315", description=>"2 Slices of Good Earth Pizza, Cheese, Turkey Sausage, (Gluten Free Option available-rice and tapioca flour), Asian Chicken and Rice with Orange Sauce"),
		array(refId=>"c0a56028606ac1f9ec4987826f932e3d", description=>"2 Slices of Good Earth Pizza, Cheese, Turkey Sausage, (Gluten Free Option available-rice and tapioca flour),Asian Chicken and rIce with Orange Sauce"),
		array(refId=>"4ad28d93a05542c1e14cddb580e09935", description=>"2 Slices of Good Earth Pizza, Cheese, Turkey Sausage. (Gluten Free Option available- Rice & Tapioca Flour)"),
		array(refId=>"1d4fd97865216470d1f081a84e6db494", description=>"Turkey Sandwiches with Chicken Noodle Soup/Chicken with Rice Soup,Hamburgers and Hot Dogs, with Potato Wedges "),
		array(refId=>"801f03843458ab8f9d4ee9cdc616a746", description=>"Turkey sub with Chicken Noodle Soup,/Chicken with Rice Soup, Hamburgers/ Hot Dogs with Potato Wedges, Chili and Rice  "),
		array(refId=>"ed2ec265c59ac4a5ea182deb81c3e2d4", description=>"Taco Bar with Turkey, Rice, Choice of Black or Pinto Beans, Cheese Enchiladas, Choice of Black or Pinto Beans"),
		array(refId=>"2a510f3c22a2b13a8a5022e754e267b8", description=>"Teriyaki Chicken rice and Broccoli, BBQ Chicken with Potatoes and Corn, Chicken Tenders with mashed potatoes"),
		array(refId=>"32b5d659acf3dd78c0bea8079dfd72f6", description=>"2 Slices of Good Earth Pizza: Cheese, Turkey Sausage, (Gluten Free Option available)Asian Chicken and Rice with Orange Sauce"),
		array(refId=>"3d72e02e4551407a0b07914862ec2f2f", description=>"Burrito, Hard or Soft Taco Bar with Rice, Beans and Toppings, Cheese or Chicken Enchilada, with Rice and Beans "),
		array(refId=>"940e96d36c8d749a38ea70c085863571", description=>"Turkey Subs with Chicken Noodle Soup, Hamburgers and Hot Dogs"),
		array(refId=>"e5d0f84f50a733e50fa0cf282de28097", description=>"Burrito, Hard or Soft Tacos, build your own Nachos"),
		array(refId=>"2ee044a47c914646d27fe8c4de223817", description=>"Spaghetti and Meat Balls, Penne Pasta with Marinara, Meat or Pesto Sauce.")
		);
		$newArray=array();
		foreach ($sourceArray as $data){
			$data['shortName']='-'.substr($data['description'], 0, 20);
			$data['name']=substr($data['description'], 0, 40);
			$data['suggestedPrice']=1000;
			$newArray[]=$data;
		}

		return $newArray;
	}

	public function genOfferingArray($sourceCount){

		$source=array(
		array(name=>"All American", price=>5650, meal=>"742ff41d83439a60b0ccbb11a80838cc"),
		array(name=>"La Fiesta", price=>6215, meal=>"cd07909845cc381e776b18f3ffcbcb8d"),
		array(name=>"Pasta Day", price=>6215, meal=>"c67f7d1f6557cd3e0f386343dacd6e0a"),
		array(name=>"Chicken Day", price=>5085, meal=>"afce52bccc1d9e83b416fd9259be438f"),
		array(name=>"Pizza Day", price=>4950, meal=>"e43da734dfb2f1f360408c522bba7e2a"),
		array(name=>"Pasta Day", price=>4165, meal=>"18615b76858d66cc3914abe0a9ade8d6"),
		array(name=>"Sub Sandwich", price=>4560, meal=>"6748b2a5d496eb4a83aac66602b1772d"),
		array(name=>"Variety Day", price=>4560, meal=>"b7ac878b02422515905af27851334f9c"),

		array(name=>"Chicken Day", price=>4950, meal=>"b3e07057380c00a5ae13b0218e2c9bc8"),
		array(name=>"Sub Sandwich", price=>4950, meal=>"a1fd1358044cde24df2cc6484d124c43"),
		array(name=>"La Fiesta", price=>5355, meal=>"464d19275016ca42fbee60daa7956ba9"),
		array(name=>"Chicken Day", price=>5085, meal=>"3a51c2b45ef0ef24a883cceb35ccfcef"),
		array(name=>" American", price=>4560, meal=>"6c3d207c4d65c8564cf35769cbe9e268"),
		array(name=>"Pasta Day", price=>4400, meal=>"1db059f066b8924112a4765fed18e593"),
		array(name=>"La Fiesta", price=>5500, meal=>"64f62871f7846fd2671626ef94277db4"),
		array(name=>"Pizza Day", price=>5310, meal=>"4949c0eb8699dc03231bc59b369005f0"),
		array(name=>"Caesar Chicken", price=>5130, meal=>"67d24f0ad01a2b994870ee0615522aaa"),
		array(name=>"Sub Sandwich", price=>5130, meal=>"096d683e2227881c9ea5161dba3f96c3"),
		array(name=>" American", price=>4760, meal=>"63ace2853e205f69a8e6619fb6b3cab5"),
		array(name=>"Chicken Day", price=>4725, meal=>"57999a27ce64e351e58568c55699820f"),
		array(name=>"Chicken Day", price=>4780, meal=>"36552b067851d0e8f3b038e565d53d38"),
		array(name=>"Taco Day", price=>5355, meal=>"71b7a2be1a71bafa4867a1d29c47b6b3"),
		array(name=>"Pizza Day", price=>5310, meal=>"4949c0eb8699dc03231bc59b369005f0"),
		array(name=>"All American", price=>5650, meal=>"1324a08767ed2d761b5c4835379e3eea"),
		array(name=>"Pizza Day", price=>5355, meal=>"870213e1f427bc7eacd7de0d26dae62b"),
		array(name=>"Pizza Day", price=>5130, meal=>"870213e1f427bc7eacd7de0d26dae62b"),
		array(name=>"Pasta Day", price=>6215, meal=>"18615b76858d66cc3914abe0a9ade8d6"),
		array(name=>"Sub Sandwich", price=>5355, meal=>"0fb037684f3707e123f1ff4bc65a1b03"),
		array(name=>"All American", price=>4950, meal=>"843f7b553a956d935dbbe13ff0d7b6fe"),
		array(name=>"Taco Day", price=>5130, meal=>"71b7a2be1a71bafa4867a1d29c47b6b3"),
		array(name=>"Pasta Day", price=>6215, meal=>"750939246d16af896952b489dd2ac800"),
		array(name=>"All American", price=>5250, meal=>"12b2a450dfac511929ec3012f1bbbc43"),
		array(name=>"Pizza Day", price=>5535, meal=>"4949c0eb8699dc03231bc59b369005f0"),
		array(name=>"Pizza Day", price=>6615, meal=>"775d9c5afa56302e2d65932eb14afd81"),
		array(name=>"La Fiesta", price=>6215, meal=>"0c43205292c58d65bc96d9fc14d80bd7"),
		array(name=>"Soup and", price=>4400, meal=>"581e29709d357618d809af4e664d687a"),
		array(name=>"La Fiesta", price=>6300, meal=>"4198b689f5d1b04538e4d7b9d9abb64b"),
		array(name=>"Pizza Day", price=>6300, meal=>"6d829f9c63d42d30755befa6a84f30f5"),
		array(name=>"Chicken Day", price=>4520, meal=>"3a51c2b45ef0ef24a883cceb35ccfcef"),
		array(name=>"Pizza Day", price=>4760, meal=>"2c42968d4f7413a9de1277c72d1992e5"),
		array(name=>"All American", price=>5355, meal=>"907c68b3575955cea3d17709c965b095"),
		array(name=>"La Fiesta", price=>6780, meal=>"4f06c6206a4f9ee658edf402e361486c"),
		array(name=>"All American", price=>5650, meal=>"12b2a450dfac511929ec3012f1bbbc43"),
		array(name=>"All American", price=>5650, meal=>"12b2a450dfac511929ec3012f1bbbc43"),
		array(name=>"Pizza Day", price=>5650, meal=>"6753e7275485ecb79f86a974afc9a772"),
		array(name=>"Pizza Day", price=>5650, meal=>"7576643483abb0e80493f635ebff8cc9"),
		array(name=>"Pizza Day", price=>6795, meal=>"4926362df7f37cc92ccecdd48c321ff5"),
		array(name=>"Chicken Caesar", price=>5130, meal=>"3b5c67b4ad05a0a327ed4a77b212b6be"),
		array(name=>"Pizza Day", price=>6215, meal=>"8889e2caf0112809967f5e2f4cc2a830"),
		array(name=>"Chicken Day", price=>3955, meal=>"57999a27ce64e351e58568c55699820f"),
		array(name=>"Pasta Day", price=>4725, meal=>"ede16b0aaac42bb2e02be85a1efe7e01"),
		array(name=>"mk", price=>5, meal=>"07d935680b6501b2e42fe4baea021389"),
		array(name=>"La Fiesta", price=>4400, meal=>"fbb5a98089295bb42aea3591e01892e8"),
		array(name=>"Chicken Day", price=>4200, meal=>"57999a27ce64e351e58568c55699820f"),
		array(name=>"Caesar Chicken", price=>5355, meal=>"1cc4c77e03188408eb2b42beda97b5cb"),
		array(name=>"All American", price=>4725, meal=>"12b2a450dfac511929ec3012f1bbbc43"),
		array(name=>"Chicken Day", price=>4725, meal=>"b8f2046cf5666c2b59a4b431269c3300"),
		array(name=>"Chicken Day", price=>5250, meal=>"57999a27ce64e351e58568c55699820f"),
		array(name=>"Pasta Day", price=>5250, meal=>"072458bc319697bde5e65f08c1bd67c9"),
		array(name=>"Pasta Day", price=>4200, meal=>"230e80e09efd7138766a4efb98e8789c"),
		array(name=>"Pizza Day", price=>5775, meal=>"6d829f9c63d42d30755befa6a84f30f5"),
		array(name=>"La Fiesta", price=>6300, meal=>"2312179f558625307a956f622f96eb51"),
		array(name=>"Pasta Day", price=>6300, meal=>"fd414d6a3bb3d3fc25a78bf2872a96d6"),
		array(name=>"Pizza Day", price=>5130, meal=>"870213e1f427bc7eacd7de0d26dae62b"),
		array(name=>"Pasta Day", price=>5355, meal=>"94d070607e8d374d9ff32d4a596e82ee"),
		array(name=>"Pasta Day", price=>5130, meal=>"003d0b96a4bf96a05286f57fd00857a8"),
		array(name=>"Pizza Day", price=>6390, meal=>"775d9c5afa56302e2d65932eb14afd81"),
		array(name=>"Pasta Day", price=>5130, meal=>"94d070607e8d374d9ff32d4a596e82ee"),
		array(name=>"All American", price=>5775, meal=>"e164298e37fbb31e433f4bced534fcab"),
		array(name=>"All American", price=>5650, meal=>"742ff41d83439a60b0ccbb11a80838cc"),
		array(name=>"La Fiesta", price=>5775, meal=>"464d19275016ca42fbee60daa7956ba9"),
		array(name=>"Pasta Day", price=>5650, meal=>"ede16b0aaac42bb2e02be85a1efe7e01"),
		array(name=>"Pizza Day", price=>5085, meal=>"225bcb0d754747f4fc743ccaf084d008"),
		array(name=>"Pasta Day", price=>3850, meal=>"072458bc319697bde5e65f08c1bd67c9"),
		array(name=>"Chicken Day", price=>4400, meal=>"36552b067851d0e8f3b038e565d53d38"),
		array(name=>"Chicken Day", price=>4520, meal=>"3a51c2b45ef0ef24a883cceb35ccfcef"),
		array(name=>"Pizza Day", price=>4400, meal=>"e43da734dfb2f1f360408c522bba7e2a"),
		array(name=>"All American", price=>5775, meal=>"12b2a450dfac511929ec3012f1bbbc43"),
		array(name=>"International Day", price=>6325, meal=>"6dd297533ceceb166304c502bcd1b315"),
		array(name=>"Pasta Day", price=>6325, meal=>"750939246d16af896952b489dd2ac800"),
		array(name=>"Pizza Day", price=>5775, meal=>"c0a56028606ac1f9ec4987826f932e3d"),
		array(name=>"La Fiesta", price=>5650, meal=>"fbb5a98089295bb42aea3591e01892e8"),
		array(name=>"Pizza Day", price=>5650, meal=>"4ad28d93a05542c1e14cddb580e09935"),
		array(name=>"All American", price=>5750, meal=>"1d4fd97865216470d1f081a84e6db494"),
		array(name=>"Pizza Day", price=>5650, meal=>"6753e7275485ecb79f86a974afc9a772"),
		array(name=>"Pasta Day", price=>6215, meal=>"230e80e09efd7138766a4efb98e8789c"),
		array(name=>"Chicken Day", price=>4520, meal=>"afce52bccc1d9e83b416fd9259be438f"),
		array(name=>"All American", price=>5250, meal=>"801f03843458ab8f9d4ee9cdc616a746"),
		array(name=>"La Fiesta", price=>6325, meal=>"ed2ec265c59ac4a5ea182deb81c3e2d4"),
		array(name=>"Pasta Day", price=>5445, meal=>"750939246d16af896952b489dd2ac800"),
		array(name=>"Chicken Day", price=>5750, meal=>"2a510f3c22a2b13a8a5022e754e267b8"),
		array(name=>"International Day", price=>5445, meal=>"32b5d659acf3dd78c0bea8079dfd72f6"),
		array(name=>"Chicken Day", price=>4950, meal=>"2a510f3c22a2b13a8a5022e754e267b8"),
		array(name=>"All American", price=>5775, meal=>"801f03843458ab8f9d4ee9cdc616a746"),
		array(name=>"Chicken Day", price=>5175, meal=>"57999a27ce64e351e58568c55699820f"),
		array(name=>"La Fiesta", price=>6300, meal=>"3d72e02e4551407a0b07914862ec2f2f"),
		array(name=>"La Fiesta", price=>6300, meal=>"4198b689f5d1b04538e4d7b9d9abb64b"),
		array(name=>"La Fiesta", price=>5775, meal=>"4198b689f5d1b04538e4d7b9d9abb64b"),
		array(name=>"All American", price=>5650, meal=>"940e96d36c8d749a38ea70c085863571"),
		array(name=>"La Fiesta", price=>5650, meal=>"4f06c6206a4f9ee658edf402e361486c"),
		array(name=>"Pizza Day", price=>5775, meal=>"225bcb0d754747f4fc743ccaf084d008"),
		array(name=>"Pizza Day", price=>6300, meal=>"225bcb0d754747f4fc743ccaf084d008"),
		array(name=>"Pasta Day", price=>3675, meal=>"750939246d16af896952b489dd2ac800"),
		array(name=>"Chicken Day", price=>3955, meal=>"afce52bccc1d9e83b416fd9259be438f"),
		array(name=>"Pasta Day", price=>4200, meal=>"750939246d16af896952b489dd2ac800"),
		array(name=>"La Fiesta", price=>6780, meal=>"4f06c6206a4f9ee658edf402e361486c"),
		array(name=>"All American", price=>5650, meal=>"940e96d36c8d749a38ea70c085863571"),
		array(name=>"Chicken Day", price=>4725, meal=>"b8f2046cf5666c2b59a4b431269c3300"),
		array(name=>"Chicken Day", price=>5250, meal=>"b8f2046cf5666c2b59a4b431269c3300"),
		array(name=>"Pasta Day", price=>5650, meal=>"230e80e09efd7138766a4efb98e8789c"),
		array(name=>"Pasta Day", price=>6300, meal=>"750939246d16af896952b489dd2ac800"),
		array(name=>"Pizza Day", price=>6390, meal=>"775d9c5afa56302e2d65932eb14afd81"),
		array(name=>"Pizza Day", price=>6570, meal=>"4926362df7f37cc92ccecdd48c321ff5"),
		array(name=>"Pasta Day", price=>5085, meal=>"072458bc319697bde5e65f08c1bd67c9"),
		array(name=>"Pizza Day", price=>6570, meal=>"4926362df7f37cc92ccecdd48c321ff5"),
		array(name=>"La Fiesta", price=>6780, meal=>"e5d0f84f50a733e50fa0cf282de28097"),
		array(name=>"Pizza Day", price=>5085, meal=>"225bcb0d754747f4fc743ccaf084d008"),
		array(name=>"Pasta Day", price=>3675, meal=>"2ee044a47c914646d27fe8c4de223817"),
		array(name=>"All American", price=>5775, meal=>"940e96d36c8d749a38ea70c085863571"),
		array(name=>"La Fiesta", price=>5445, meal=>"464d19275016ca42fbee60daa7956ba9")
		);

			$list=$source;
			$outList=array();
			for ($i=0, $len=$sourceCount; $i<$len; $i++){
				$outList[]=$list[$i];
			}
			$selectSource=$outList;

		$outArray=array();
		foreach ($selectSource as $data){
			$name=$data['name'];

			$new=array();
			$new['day']=array('1', '2');
			$new['gradeLevel']='First';
			$new['school']='SaintMarks';
			$new['name']=$name." {$new['day']}";
			$new['price']=$data['price'];
			$new['meal']=$data['meal'];
			$outArray[]=$new;

			$new=array();
			$new['day']=array('3', '4');
			$new['gradeLevel']=array('Second', 'Third');
			$new['school']=array('MarinHorizon', 'NeilCummins');
			$new['name']=$name." marin, neil, 2nd, 3rd, wed, thurs";
			$new['price']=$data['price'];
			$new['meal']=$data['meal'];
			$outArray[]=$new;

			$new=array();
			$new['day']='3';
			$new['gradeLevel']='Third';
			$new['school']='LyceeFrancais';
			$new['name']=$name." {$new['day']}";
			$new['price']=$data['price'];
			$new['meal']=$data['meal'];
			$outArray[]=$new;

			$new=array();
			$new['day']='4';
			$new['gradeLevel']='Fourth';
			$new['school']='LyceeFrancais';
			$new['name']=$name." {$new['day']}";
			$new['price']=$data['price'];
			$new['meal']=$data['meal'];
			$outArray[]=$new;

		}

		return $outArray;
	}

}









