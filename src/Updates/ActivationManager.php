<?php

namespace App\Updates;

use App\Service\EmailMessage;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ActivationManager
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

	public function sendActivationEmail($emailToReceive, $username)
	{

		$message = (new \Swift_Message('Confirm your Email address'))
		  ->setFrom('info@muske.co.ke')
		  ->setTo($emailToReceive)
		  ->setBody(
			$this->templating->render('email/confirmemail.html.twig', ['username' => $username]),'text/html'
		);

		// return $this->mailer->send($message) > 0;
		return true;
	}   

	
}