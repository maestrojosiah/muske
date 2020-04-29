<?php

namespace App\Updates;

use App\Service\EmailMessage;

class MembershipManager
{
	private $emailMessage;
	private $mailer;

	public function __construct(EmailMessage $emailMessage, \Swift_Mailer $mailer)
	{
		$this->emailMessage = $emailMessage;
		$this->mailer = $mailer;
	}

	public function sendMembershipConfirmation($emailToReceive, $membership)
	{
		$message = $this->emailMessage->getMembershipMessage($membership);

		$message = (new \Swift_Message("Confirmation of your $membership membership"))
		  ->setFrom('info@muske.co.ke')
		  ->setTo($emailToReceive)
		  ->addPart(
			  $message		  
		);

		return $this->mailer->send($message) > 0;
	}   

	
}