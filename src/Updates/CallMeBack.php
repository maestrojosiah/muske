<?php

namespace App\Updates;

use App\Service\EmailMessage;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CallMeBack
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

	public function sendEmailMessage($emailToReceive, $fullname, $client_phone_number, $username)
	{

		$message = (new \Swift_Message("Call back request"))
		  ->setFrom('info@muske.co.ke')
		  ->setTo($emailToReceive)
		  ->setBody(
			$this->templating->render('email/call_back.html.twig', [
				'fullname' => $fullname, 
				'client_phone_number' => $client_phone_number,
				'username' => $username,
			]),'text/html'
		);

		return $this->mailer->send($message) > 0;
		// return true;
	}   

	
}