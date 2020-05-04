<?php

namespace App\Updates;

use App\Service\EmailMessage;

class ActivationManager
{
	private $emailMessage;
	private $mailer;

	public function __construct(EmailMessage $emailMessage, \Swift_Mailer $mailer)
	{
		$this->emailMessage = $emailMessage;
		$this->mailer = $mailer;
	}

	public function sendActivationEmail($emailToReceive, $username)
	{
		$message = $this->emailMessage->getActivationMessage($username);

		$message = (new \Swift_Message('Activate your MuSKe account'))
		  ->setFrom('info@muske.co.ke')
		  ->setTo($emailToReceive)
		  ->addPart(
			  $message
		);

		// return $this->mailer->send($message) > 0;
		return true;
	}   

	
}