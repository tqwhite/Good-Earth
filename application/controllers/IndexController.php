<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // not yet implemented, see /pictures instead
    }

    public function emailTestAction()
    {

        error_reporting(E_ERROR | E_WARNING | E_PARSE);
		$mail = new Zend_Mail_Storage_Pop3(array('host'     => 'mail.justkidding.com',
												 'user'     => 'tq@justkidding.com',
												 'password' => 'tq31415'));

		$count=1;
		echo $mail->countMessages() . " messages found\n";
		foreach ($mail as $message) {
			echo "Mail from ($count) '{$message->from}': {$message->subject}\n";
			$count++;
		}


    	//print_r($mail);

    }


}





