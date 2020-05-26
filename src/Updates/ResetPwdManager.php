<?php

namespace App\Updates;

use App\Service\EmailMessage;

class ResetPwdManager
{
	private $emailMessage;
	private $mailer;
	private $templating;

	public function __construct(\Twig\Environment $templating, EmailMessage $emailMessage, \Swift_Mailer $mailer)
	{
		$this->emailMessage = $emailMessage;
		$this->mailer = $mailer;
        $this->templating = $templating;		
	}

	public function sendResetEmail($emailToReceive, $username)
	{
		// $message = $this->emailMessage->getResetMessage($username);


		$message = (new \Swift_Message('You have reset your password'))
		  ->setFrom('info@muske.co.ke')
		  ->setTo($emailToReceive)
		  ->setBody(
			$this->templating->render('email/password_reset.html.twig', ['username' => $username]),'text/html'
		);

		return $this->mailer->send($message) > 0;
		// return true;
	}   

	
}