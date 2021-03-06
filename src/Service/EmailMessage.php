<?php 

namespace App\Service;

use Psr\Log\LoggerInterface;

class EmailMessage
{

	public function getResetMessage($username)
	{
		$message = "You have requested to reset your password. \\n";
		$message .= "Here is the link: https://muske.co.ke/musician/password/new/$username";

		return $message;
	}

	public function getMembershipMessage($membership){
		$message = "This is a confirmation message that you are now a $membership member.";
		$message .= "We will call you as soon as possible to confirm some details.";
		return $message;
	}

	public function getActivationMessage($username){
		$message = "You have created an account at https://muske.co.ke. \\n";
		$message .= "Please activate your account with this link: https://muske.co.ke/musician/activate/$username within 48 hours";

		return $message;
	}
}