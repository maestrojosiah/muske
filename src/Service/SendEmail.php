<?php 

namespace App\Service;

use Psr\Log\LoggerInterface;

class SendEmail
{

	public function getMessage()
	{		
		$message = "You have requested to reset your password.";

		return $message;
	}
}