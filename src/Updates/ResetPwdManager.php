<?php

namespace App\Updates;

use App\Service\EmailMessage;

class ResetPwdManager
{
	private $emailMessage;
	private $mailer;

	public function __construct(EmailMessage $emailMessage, \Swift_Mailer $mailer)
	{
		$this->emailMessage = $emailMessage;
		$this->mailer = $mailer;
	}

	public function sendResetEmail($emailToReceive, $username)
	{
		$message = $this->emailMessage->getResetMessage($username);


		$message = (new \Swift_Message('You have reset your password'))
		  ->setFrom('info@muske.co.ke')
		  ->setTo($emailToReceive)
		  ->addPart(
			  $message
		);

		// return $this->mailer->send($message) > 0;
		return true;
	}   

	
}