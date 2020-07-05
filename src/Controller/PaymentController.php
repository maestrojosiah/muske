<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Payment;
use App\Repository\PaymentRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class PaymentController extends AbstractController
{
    /**
     * @Route("/payment/index/push", name="payment")
     */
    public function index()
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }


    /**
     * @Route("/get/o/auth", name="getoauth")
     */
    public function getOAuth(){

        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $key = $this->getParameter('mpesa_key');
        $pass = $this->getParameter('mpesa_secret');
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $credentials = base64_encode($key.':'.$pass);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); //setting a custom header
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $curl_response = curl_exec($curl);
        
        // $auth = json_decode($curl_response)->access_token;

        return new JsonResponse($curl_response);

    }
    /**
     * @Route("payment/stk_push/leepah", name="leepahnapush")
     */
    public function stkPush()
    {

        $BusinessShortCode = "174379";
        $LipaNaMpesaPasskey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $Timestamp = "20180409093002";
        $TransactionType = "CustomerPayBillOnline";
        $Amount = "1";
        $PartyA = "254705285959";
        $PartyB = "174379";
        $PhoneNumber = "254705285959";
        $CallBackURL = "https://muske.co.ke/get/status/pmt";
        $AccountReference = "Pro Activation";
        $TransactionDesc = "Payment for MuSKe pro account";
        $Remarks = "muskedotcodotke";
    
      
        $mpesa= new \Safaricom\Mpesa\Mpesa();

        $stkPushSimulation = $mpesa->STKPushSimulation($BusinessShortCode, $LipaNaMpesaPasskey, $TransactionType, $Amount, $PartyA, $PartyB, $PhoneNumber, $CallBackURL, $AccountReference, $TransactionDesc, $Remarks);
        $data = json_decode($stkPushSimulation);

        $entityManager = $this->getDoctrine()->getManager();
        $payment = new Payment();
        $payment->setMerchantrequestid($data->MerchantRequestID);
        $payment->setCheckoutrequestid($data->CheckoutRequestID);
        $payment->setResponsecode($data->ResponseCode);
        $payment->setResponsedescription($data->ResponseDescription);
        $payment->setCustomermessage($data->CustomerMessage);
        $entityManager->persist($payment);
        $entityManager->flush();

        // $status = $this->checkStatus($data->CheckoutRequestID);

        return new JsonResponse($data);

    }
    

    /**
     * @Route("payment/stk_push/check/status", name="leepahnapush_status")
     */
    public function checkStatus() {

        $mpesa= new \Safaricom\Mpesa\Mpesa();

        // $payment = $this->getDoctrine()->getManager()->getRepository('App:Payment')->findOneById();
        $checkoutRequestID = "ws_CO_050720201338594217";
        $BusinessShortCode = "174379";
        $LipaNaMpesaPasskey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $timestamp='20'.date("ymdhis");
        $password=base64_encode($BusinessShortCode.$LipaNaMpesaPasskey.$timestamp);
        $STKPushRequestStatus=$mpesa->STKPushQuery($checkoutRequestID,$BusinessShortCode,$password,$timestamp);

        $status = json_decode($STKPushRequestStatus);
        
        return new JsonResponse($status);

    }

    /**
     * @Route("/get/status/pmt", name="get_status")
     */
    public function callBack() {
               
        if($json = json_decode(file_get_contents("php://input"), true)) {

            $CheckoutRequestID = $this->getVar($json, 'CheckoutRequestID', 2);
            $Amount = $this->getVar($json, 'Amount', 3);
            $MpesaReceiptNumber = $this->getVar($json, 'MpesaReceiptNumber', 3);
            $TransactionDate = $this->getVar($json, 'TransactionDate', 3);
            $PhoneNumber = $this->getVar($json, 'PhoneNumber', 3);
    
            $entityManager = $this->getDoctrine()->getManager();
            $payment = $entityManager->getRepository('App:Payment')->findOneByCheckoutrequestid($CheckoutRequestID);
            $payment->setCallbackmetadata($CheckoutRequestID);
            $payment->setMpesaReceiptNumber($MpesaReceiptNumber);
            $payment->setTransactionDate($TransactionDate);
            $payment->setAmount($Amount);
            $payment->setPhoneNumber($PhoneNumber);
            $entityManager->persist($payment);
            $entityManager->flush();        

        } else {

            $json = $_POST;
            $CheckoutRequestID = $this->getVar($json, 'CheckoutRequestID', 2);

            $Amount = $this->getVar($json, 'Amount', 3);
            $MpesaReceiptNumber = $this->getVar($json, 'MpesaReceiptNumber', 3);
            $TransactionDate = $this->getVar($json, 'TransactionDate', 3);
            $PhoneNumber = $this->getVar($json, 'PhoneNumber', 3);
    
            $entityManager = $this->getDoctrine()->getManager();
            $payment = $entityManager->getRepository('App:Payment')->findOneByCheckoutrequestid($CheckoutRequestID);
            $payment->setCallbackmetadata($CheckoutRequestID);
            $payment->setMpesaReceiptNumber($MpesaReceiptNumber);
            $payment->setTransactionDate($TransactionDate);
            $payment->setAmount($Amount);
            $payment->setPhoneNumber($PhoneNumber);
            $entityManager->persist($payment);
            $entityManager->flush();        

        }
        
        return new JsonResponse('true');

    }

    function getVar($hay, $needle, $level){

        // body and stkCallback
        $body = $hay['Body'];
        $stkCallback = $body['stkCallback'];

        // depending on array level provided to 3rd parameter,
        // give back the carrier which contains the value
        // given as the second parameter
        if($level == 0){
            $carrier = $body;
        }
        if($level == 1){
            $carrier = $stkCallback;
        }
        if($level == 2){
            $carrier = $stkCallback[$needle];
        }
        if($level == 3){
            $Item = $stkCallback['CallbackMetadata']['Item'];
            $columns = array_column($Item, 'Value', 'Name');
            $carrier = $columns[$needle];
        }

        return $carrier;
    }

    public function endTransaction() {

        $mpesa= new \Safaricom\Mpesa\Mpesa();

        $callbackData=$mpesa->finishTransaction();


    }

    // public function comments(){
        // $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        // $key = $this->getParameter('mpesa_key');
        // $pass = $this->getParameter('mpesa_secret');
        
        // $curl = curl_init();
        // curl_setopt($curl, CURLOPT_URL, $url);
        // $credentials = base64_encode($key.':'.$pass);
        // curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); //setting a custom header
        // curl_setopt($curl, CURLOPT_HEADER, true);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        // $curl_response = curl_exec($curl);
        
        // $auth = json_decode($curl_response);

        // $path = "../public/cert.cer";
        // $fp=fopen($path,"r");
        // $publicKey=fread($fp,8192);
        // fclose($fp);
        // $plaintext = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        
        // openssl_public_encrypt($plaintext, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING);
        
        // $encryp =  base64_encode($encrypted);

    // }
}
