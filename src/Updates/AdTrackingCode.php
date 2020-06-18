<?php

namespace App\Updates;

use App\Service\EmailMessage;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AdTrackingCode
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

	public function sendEmailMessage($emailToReceive, $institution, $code)
	{

		$message = (new \Swift_Message("Advert Tracking Code"))
		  ->setFrom('info@muske.co.ke')
		  ->setTo($emailToReceive)
		  ->setBody(
			$this->templating->render('email/ad_track_code.html.twig', [
				'institution' => $institution, 
				'code' => $code,
			]),'text/html'
		);

		// return $this->mailer->send($message) > 0;
		return true;
	}   

	
}