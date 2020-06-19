<?php

namespace App\Updates;

use App\Service\EmailMessage;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SubmittedResume
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

	public function sendEmailMessage($emailToReceive, $institution, $username, $attachment)
	{

		$message = (new \Swift_Message("Resume from MuSKe"))
		  ->setFrom('info@muske.co.ke')
		  ->setTo($emailToReceive)
		  ->attach($attachment)
		  ->setBody(
			$this->templating->render('email/resume.html.twig', [
				'institution' => $institution, 
			]),'text/html'
		  
		);

		return $this->mailer->send($message) > 0;
		// return true;
	}   

	
}