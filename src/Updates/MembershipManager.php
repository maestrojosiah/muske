<?php

namespace App\Updates;

use App\Service\EmailMessage;

class MembershipManager
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

	public function sendMembershipConfirmation($emailToReceive, $membership, $username)
	{
		// $message = $this->emailMessage->getMembershipMessage($membership);

		$message = (new \Swift_Message("Confirmation of your $membership membership"))
		  ->setFrom('info@muske.co.ke')
		  ->setTo($emailToReceive)
		  ->setBody(
			$this->templating->render('email/account_type.html.twig', ['username' => $username, 'membership' => $membership]),'text/html'
		);

		return $this->mailer->send($message) > 0;
		// return true;
	}   

	
}