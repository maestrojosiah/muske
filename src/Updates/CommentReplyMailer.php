<?php

namespace App\Updates;

use App\Service\EmailMessage;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CommentReplyMailer
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

	public function sendEmailMessage($emailToReceive, $fullname, $replier_email, $replier_name, $content, $contentType)
	{

		$message = (new \Swift_Message("MuSKe blog activity"))
		  ->setFrom('info@muske.co.ke')
		  ->setTo($emailToReceive)
		  ->setBody(
			$this->templating->render('email/comment_reply_mail.html.twig', [
				'fullname' => $fullname, 
				'replier_name' => $replier_name,
				'email' => $replier_email,
				'content' => $content,
				'contentType' => $contentType,
			]),'text/html'
		);

		return $this->mailer->send($message) > 0;
		// return true;
	}   

	
}