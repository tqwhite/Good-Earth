<?php

class Application_Model_Offering
{

public function addDay($dayEntity){
	$offeringDayNodeEntity=new GE\Entity\OfferingDayNode();
	$offeringDayNodeEntity->day=$dayEntity;
	$offeringDayNodeEntity->offering=$offeringEntity;
	$em->persist($offeringDayNodeEntity);
}
}

