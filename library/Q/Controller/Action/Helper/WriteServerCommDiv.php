<?php
 class Q_Controller_Action_Helper_WriteServerCommDiv extends Zend_Controller_Action_Helper_Abstract {

	 public function direct($serverCommList, $commDivId='serverData') {
		$dataString='';
		foreach($serverCommList as $datum){

			$dataString.="<input type=hidden name='{$datum['fieldName']}' value='{$datum['value']}'>";
		}
		return	"<div id='serverData' style='display:block;'>$dataString</div>";

	 }

 }