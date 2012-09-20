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

			$cmd="php ../scripts/doctrine.php orm:schema-tool:update --dump-sql;";
			$result=shell_exec($cmd);

			if (strlen($result)<2){$result="database is up to date. no changes required.";}

			$out=str_replace(';', ';<p/>', $result);
			$out=str_replace(',', ',<br/>&nbsp;&nbsp;&nbsp;&nbsp;', $out);
			echo "<div style='font-family:sans-serif;font-size:10pt;margin:40px 0px 0px 50px;'>$out</div>";
			exit;
		}
    }


}





