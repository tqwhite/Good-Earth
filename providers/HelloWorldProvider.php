<?php

require_once 'Zend/Tool/Project/Provider/Abstract.php';
require_once 'Zend/Tool/Project/Provider/Exception.php';

class HelloWorldProvider extends Zend_Tool_Project_Provider_Abstract
{



public function say()
    {
    
        echo 'Hello World!';
    
    }

public function test()
    {
      error_reporting(E_ERROR | E_WARNING | E_PARSE);
		$mail = new Zend_Mail_Storage_Pop3(array('host'     => 'mail.tqwhite.com',
												 'user'     => 'tq@tqwhite.com',
												 'password' => 'tq3141'));
		
		$count=1;
		echo $mail->countMessages() . " messages found\n";
		foreach ($mail as $message) {
			echo "Mail from ($count) '{$message->from}': {$message->subject}\n";
			$count++;
		}
    	
    	$message = $mail->getMessage(8)->getContent();
    	
    	print_r("\n\n".$message);
    	//print_r($mail);    	//print_r($mail);
    	
    			
		$tr=new Zend_Mail_Transport_Sendmail();
	
		$mail->setBodyHtml($message);
		$mail->setFrom('tq@justkidding.com', "TQ's Awesome Email System");
		$mail->setSubject("New Email System Testing");
		
		$mail->addTo('tq@justkidding.com', 'TQ White II');

}
    
}

