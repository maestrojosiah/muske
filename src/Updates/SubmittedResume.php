<?php

namespace App\Updates;

use App\Service\EmailMessage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Knp\Snappy\Pdf;

class SubmittedResume
{
	private $emailMessage;
	private $mailer;
	private $templating;
	private $pdf;
	
	public function __construct(\Twig\Environment $templating, EmailMessage $emailMessage, \Swift_Mailer $mailer, Pdf $pdf)
	{
		$this->emailMessage = $emailMessage;
		$this->mailer = $mailer;
        $this->templating = $templating;		
        $this->pdf = $pdf;		
	}

	public function sendEmailMessage($emailToReceive, $institution, $code, $fullname, $username)
	{
        $snappy = $this->pdf;
        $filename = "Resume-$fullname";
        $url = 'http://127.0.0.1:8000/maestrojosiah/pdf';
        $pdf = $snappy->getOutput($url);

		$message = (new \Swift_Message("Advert Tracking Code"))
		  ->setFrom('info@muske.co.ke')
		  ->setTo($emailToReceive)
		  ->setBody(
			$this->templating->render('email/ad_track_code.html.twig', [
				'institution' => $institution, 
				'code' => $code,
			]),'text/html'
		  ->attach($pdf, $filename)
		);

		// return $this->mailer->send($message) > 0;
		return true;
	}   

	
}