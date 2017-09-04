<?php

class UtilityController extends Zend_Controller_Action
{

    private $doctrineContainer = null;
    private $entityManager;

    public function init()
    {
		$this->doctrineContainer=Zend_Registry::get('doctrine');
		$this->em=$this->doctrineContainer->getEntityManager();
		$this->entityManager=$this->em;
    }
    
    public function timeoutAction(){
    

		error_log("TIMEOUT: account: {$_POST['account']['refId']}, purchase: {$_POST['purchase']['refId']}, , familyName: {$_POST['account']['familyName']}");

		$mail = new Zend_Mail();

		$emailSender=Zend_Registry::get('emailSender');
    
    	if (!$emailSender){
			$tr=new Zend_Mail_Transport_Sendmail();
		}
		else{
			$tr=new Zend_Mail_Transport_Smtp($emailSender['hostName'], array(
				'username'=>$emailSender['authSet']['username'],
				'password'=>$emailSender['authSet']['password'],
				'port'=>$emailSender['authSet']['port'],
				'ssl'=>$emailSender['authSet']['ssl'],
				'auth'=>$emailSender['authSet']['auth']
			));

		}

		$emailMessage = \Q\Utils::dumpWebString($_POST, "$"."timeoutData");
		$emailMessage = "&lt;?php<br> $emailMessage";
		$mail->setBodyHtml($emailMessage);
		$mail->setFrom($emailSender['fromAddress'], $emailSender['fromName']);
		$mail->setSubject("Good Earth: TIMEOUT Error Report");

		$mail->addTo('tq@justkidding.com', 'tq white ii');

		$mail->send($tr);

		$this->_helper->json(array(status => 1, messages => $emailMessage, data => array()));

    }

    public function indexAction()
    {
        // action body
    }

    public function exportAction()
    {

    	ob_start();

    	$dataObj=new \Application_Model_Export();
		$purchaseData=$dataObj->collectPurchases();
		$dataList=$purchaseData['exportData'];

		if (count($dataList)>0){
			$tableArray=$dataObj->getTableData($dataList, 'accounts users students orders purchases accountPurchaseNodes purchaseOrderNodes');

echo "accountCount=".count($tableArray['accounts'])."<BR>";
echo "accountPurchaseNodeCount=".count($tableArray['accountPurchaseNodes'])."<BR>";
echo "userCount=".count($tableArray['users'])."<BR>";
echo "studentCount=".count($tableArray['students'])."<BR>";

echo "purchaseCount=".count($tableArray['purchases'])."<BR>";
echo "purchaseOrderNodeCount=".count($tableArray['purchaseOrderNodes'])."<BR>";
echo "orderCount=".count($tableArray['orders'])."<BR>";

\Q\Utils::dumpWeb($tableArray, 'tableArray');

			$result=$dataObj->write($tableArray);

		}
		else{
			echo "NO NEW DATA IS READY FOR HELIX. NOTHING SENT.<p/>\n\n";
			$result=true;
		}

		$listing=ob_get_contents();
			ob_end_clean();

		if ($result){
			echo "Exported to helix ".date("Y-m-d H:i:s")."<p/>\n\n\n";
			echo "start of purchaseId list <br/>\n";

			foreach ($purchaseData['entityList'] as $purchase){

  					$purchase->alreadyInHelix=true;
  					$this->entityManager->persist($purchase);
 					echo "purchaseRefId {$purchase->refId}<br/>\n";
			}
			$this->entityManager->flush();

			echo "end of purchaseId list <p/>\n\n\n";
		}
		else{
			echo "HELIX ERROR, purchases will all be resent next time<p/>\n\n";
		}

		echo "start transcript<p/>\n\n";
			echo $listing;
			echo "end transcript<br/>\n";
		exit;
    }

    public function migrateAction()
    {
		$updateSchema=$this->getRequest()->getParam('updateSchema');

		if($updateSchema=='pleaseRiskMyHappiness'){

			echo "Doing these things...<p/>";

			$cmd="php ../scripts/doctrine.php orm:schema-tool:update --dump-sql;";
			$result=shell_exec($cmd);

			if (strlen($result)<2){$result="database is up to date. no changes required.";}

			$out=str_replace(';', ';<p/>', $result);
			$out=str_replace(',', ',<br/>&nbsp;&nbsp;&nbsp;&nbsp;', $out);
			echo "<div style='font-family:sans-serif;font-size:10pt;margin:40px 0px 0px 50px;'>$out</div>";

			//if you run the code below, it will actually change the database, SO DON'T

			$em=$this->doctrineContainer->getEntityManager();
			$classes=array(
				$em->getClassMetadata('GE\Entity\Account'),
				$em->getClassMetadata('GE\Entity\AccountPurchaseNode'),
				$em->getClassMetadata('GE\Entity\Day'),
				$em->getClassMetadata('GE\Entity\GradeLevel'),
				$em->getClassMetadata('GE\Entity\GradeSchoolNode'),
				$em->getClassMetadata('GE\Entity\Meal'),
				$em->getClassMetadata('GE\Entity\Offering'),
				$em->getClassMetadata('GE\Entity\OfferingDayNode'),
				$em->getClassMetadata('GE\Entity\OfferingGradeLevelNode'),
				$em->getClassMetadata('GE\Entity\OfferingSchoolNode'),
				$em->getClassMetadata('GE\Entity\Order'),
				$em->getClassMetadata('GE\Entity\Purchase'),
				$em->getClassMetadata('GE\Entity\PurchaseOrderNode'),
				$em->getClassMetadata('GE\Entity\School'),
				$em->getClassMetadata('GE\Entity\Student'),
				$em->getClassMetadata('GE\Entity\User')
			);
			$tool = new \Doctrine\ORM\Tools\SchemaTool($em);
			echo $tool->updateSchema($classes, true);
			echo '<p/>And they are done.<BR>';
			exit;
		}
		else{

			//for future reference, doctrine.php has a literal path to application.ini specific to the current application file structure
			$cmd="php ../scripts/doctrine.php orm:schema-tool:update --dump-sql;";
			$result=shell_exec($cmd);

			if (strlen($result)<2){$result="database is up to date. no changes required.";}

			$out=str_replace(';', ';<p/>', $result);
			$out=str_replace(',', ',<br/>&nbsp;&nbsp;&nbsp;&nbsp;', $out);
			echo "<div style='font-family:sans-serif;font-size:10pt;margin:40px 0px 0px 50px;'>$out</div>";
			exit;
		}
    }
    
    public function dupesAction(){
    
		$specs=Zend_Registry::get('databaseSpecs');

		$db = new Zend_Db_Adapter_Pdo_Mysql(array(
			'host'     => $specs['host'],
			'username' => $specs['user'],
			'password' => $specs['password'],
			'dbname'   => $specs['dbname']
		));

		$query="select 
#apn.purchaseRefId, apn.accountRefId, a.familyName, o.created as 'order', 
eaters.lastName, eaters.firstName, a.familyName, p.created, 
(select count(*) from students where accountRefId=eaters.accountRefId and firstName=eaters.firstName) as count
, a.refId

from accounts as a

left join students as eaters on eaters.accountRefId=a.refId
left join users as u on u.accountRefId=a.refId

left join gradeLevels as gl on gl.refId=eaters.gradeLevelRefId
left join schools as s on s.refId=eaters.schoolRefId

left join orders as o on o.studentRefId=eaters.refId
left join offerings as of on of.refId=o.offeringRefId
left join meals as m on m.refId=of.mealRefId

left join offeringDayNodes as odn on odn.offeringRefId=of.refId
left join days as d on d.refId=odn.dayRefId

left join purchaseOrderNodes as pon on pon.orderRefId=o.refId
left join purchases as p on p.refId=pon.purchaseRefId

left join accountPurchaseNodes as apn on apn.purchaseRefId=p.refId

where not isnull(p.refId)
and (select count(*) from students where accountRefId=eaters.accountRefId and firstName=eaters.firstName and isActiveFlag=true)>1
and p.refId>'2014-07-01'

group by eaters. firstName, eaters.refId, p.refId

order by a.familyName, eaters.firstName, p.created
limit 100000";
		$stmt = $db->query($query);
		Zend_Debug::dump($stmt->fetchAll());

		echo "<hr/>";exit;

    }
    
    public function pinghelixAction(){
    $ipAddress=$this->getRequest()->getQuery('helixIp');
    	$helixConfiguration=\Zend_Registry::get('helixConfiguration');

	if ($ipAddress){

    	$helixConfiguration['hostIp']=$ipAddress;

    	\Zend_Registry::set('helixConfiguration', $helixConfiguration);
    	$helixConfiguration2=\Zend_Registry::get('helixConfiguration');
    	$serverMessage="<div style='color:black;margin-bottom:10px;'>IP Address (from URL)={$ipAddress}</div>";
    }
    else{

    	$serverMessage="<div style='background:yellow;margin-bottom:10px;'>IP Address (from configuration file)={$helixConfiguration['hostIp']}</div>";
    }

	$this->connection=new \Heliport\ServerInterface();
	$helix_status = $this->connection->ihr190();
	$releaseStatus=$this->connection->releasePoolUser();

if ($helix_status){
echo "<div style='color:black;background:#9f6;font-weight:bold;margin:10px 0px;'>Helix Accessed Successfully</div>";
}
else{
echo "<div style='color:black;background:#f96;font-weight:bold;margin:10px 0px;'>Fail. Helix not responding.</div>";
}
echo $serverMessage;

echo "<div style='color:black;margin-bottom:10px;'>helix_status (ihr190)={$helix_status}</div>";

echo "<div style='color:black;margin-bottom:10px;'>pool user releaseStatus={$releaseStatus}</div>";

if ($_SERVER['SERVER_PORT']=='80'){
$scheme='http';
}
else{

$scheme='https';
}
$url="$scheme://{$_SERVER['HTTP_HOST']}/utility/pingHelix?helixIp={$helixConfiguration['hostIp']}";

echo "<div style='margin:35px 0px;'><a href='$url'>$url</a></div>";

exit;
    }

}

