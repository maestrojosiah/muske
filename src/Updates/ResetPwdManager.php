<?php

namespace App\Updates;

use App\Service\SendEmail;

class ResetPwdManager
{
	private $SendEmail;
	private $mailer;

	public function __construct(SendEmail $SendEmail, \Swift_Mailer $mailer)
	{
		$this->SendEmail = $SendEmail;
		$this->mailer = $mailer;
	}

	public function sendResetEmail($emailToReceive)
	{
		$message = $this->SendEmail->getMessage();

		$message = (new \Swift_Message('You have reset your password'))
		  ->setFrom('info@muske.co.ke')
		  ->setTo($emailToReceive)
		  ->addPart(
		  	$message
		  );

		return $this->mailer->send($message) > 0;
	}   

}