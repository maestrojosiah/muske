<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\SendSms;

class TextingController extends AbstractController
{
    /**
     * @Route("/texting/send/sms", name="texting")
     */
    public function index(SendSms $sendSms, Request $request)
    {
        if(null != $request->request->get('to')){
            $to = $request->request->get('to');
            $msg = $request->request->get('msg');
    
            if ($to !== "" && $msg !== "") {
    
                $res = [];
                $data = "{\n \"from\":\"ModuleZilla\",\n \"to\":\"$to\",\n \"text\":\"$msg\"\n}";
    
                try {
                    $response = $sendSms->CallAPI('POST', 'http://54.247.191.102/restapi/sms/1/text/single', $data );
                    $res = $response;
                } catch (HttpException $ex) {
                    echo $ex;
                }
    
                return new JsonResponse($res);
    
            }
    
    
        } else {
            
            return $this->render('index/test.html.twig', [
                'controller_name' => 'TextingController',
            ]);
    
        }

    }


}
