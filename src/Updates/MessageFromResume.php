<?php

namespace App\Updates;

use App\Service\EmailMessage;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MessageFromResume
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

	public function sendEmailMessage($emailToReceive, $username, $sender, $senderemail, $message, $senderphone, $calltime)
	{

		$message = (new \Swift_Message("Message from your online resume"))
		  ->setFrom('info@muske.co.ke')
		  ->setTo($emailToReceive)
		  ->setBody(
			$this->templating->render('email/messagefromresume.html.twig', [
				'username' => $username, 
				'message' => $message,
				'sendername' => $sender,
				'senderemail' => $senderemail,
				'senderphone' => $senderphone,
				'calltime' => $calltime,
				
			]),'text/html'
		);

		return $this->mailer->send($message) > 0;
		// return true;
	}   

	
}