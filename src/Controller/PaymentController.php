<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Payment;
use App\Entity\Callback;
use App\Repository\CallbackRepository;
use App\Repository\PaymentRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Repository\ProRepository;
use App\Updates\MembershipManager;

class PaymentController extends AbstractController
{
    /**
     * @Route("/payment/index/push", name="payment")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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
     * @Route("/payment/stk_push/leepah", name="leepahnapush")
     */
    public function stkPush(Request $request)
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $phonenumber = $this->formatPhoneNumber($request->request->get('phonenumber'));

        $BusinessShortCode = "174379";
        $LipaNaMpesaPasskey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $Timestamp = "20180409093002";
        $TransactionType = "CustomerPayBillOnline";
        $Amount = "1";
        $PartyA = $phonenumber;
        $PartyB = "174379";
        $PhoneNumber = $phonenumber;
        $CallBackURL = "https://muske.co.ke/touch/script/pmt";
        $AccountReference = "Pro Activation";
        $TransactionDesc = "Payment for MuSKe pro account";
        $Remarks = "muskedotcodotke";
    
      
        $mpesa= new \Safaricom\Mpesa\Mpesa();

        $stkPushSimulation = $mpesa->STKPushSimulation($BusinessShortCode, $LipaNaMpesaPasskey, $TransactionType, $Amount, $PartyA, $PartyB, $PhoneNumber, $CallBackURL, $AccountReference, $TransactionDesc, $Remarks);
        
        $data = json_decode($stkPushSimulation);
        $errors = [];
        if (isset($data->errorMessage)) {
            if($data->errorMessage == "error on exit from activity, no matching transition"){
                $msg = "Number is invalid. Please use an M-pesa activated number.";
                $errors['error'] = $msg;
            } else {
                $msg = $data->errorMessage;
                $errors['error'] = $msg;
            }
            $ret = ['status' => 'failed', 'errors' => $errors];
            return new JsonResponse($ret);
        } else {

            $entityManager = $this->getDoctrine()->getManager();
            $payment = new Payment();
            $payment->setMerchantrequestid($data->MerchantRequestID);
            $payment->setCheckoutrequestid($data->CheckoutRequestID);
            $payment->setResponsecode($data->ResponseCode);
            $payment->setResponsedescription($data->ResponseDescription);
            $payment->setCustomermessage($data->CustomerMessage);
            $entityManager->persist($payment);
            $entityManager->flush();
    
            $ret = ['status' => 'pending', 'id' => $data->CheckoutRequestID];
            return new JsonResponse($ret);
    
        }

    }
    

    /**
     * @Route("/payment/stk_push/check/status", name="leepahnapush_status")
     */
    public function checkStatus(MembershipManager $membershipManager, Request $request, ProRepository $proRepository) {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $mpesa= new \Safaricom\Mpesa\Mpesa();
        $musician = $this->getUser();
        $errors = [];

        $checkoutRequestID = $request->request->get('CheckoutRequestID');
        $BusinessShortCode = "174379";
        $LipaNaMpesaPasskey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $timestamp='20'.date("ymdhis");
        $password=base64_encode($BusinessShortCode.$LipaNaMpesaPasskey.$timestamp);
        $STKPushRequestStatus=$mpesa->STKPushQuery($checkoutRequestID,$BusinessShortCode,$password,$timestamp);

        $status = json_decode($STKPushRequestStatus);
        
        if (isset($status->errorMessage)) {
            
            $msg = $status->errorMessage;
            $errors['error'] = $msg;
        
            $ret = ['status' => 'failed', 'errors' => $errors];
            return new JsonResponse($ret);
        } else {

            $entityManager = $this->getDoctrine()->getManager();
            $payment = $entityManager->getRepository('App:Payment')->findOneByCheckoutrequestid($checkoutRequestID);
            $payment->setResultcode($status->ResultCode);
            $payment->setResultdesc($status->ResultDesc);
            $entityManager->persist($payment);
            $entityManager->flush();        

            if($status->ResultCode == 0){
                $membership = 'pro';
                $started = new \DateTime("now");
                $started ->setTime(0, 0, 0);
        
                $ending = clone $started;
                $ending->modify('+365 days'); 
        
                $prevPro = $proRepository->findOneBy(
                    array('musician' => $musician)
                );
                
                if($prevPro) {
                    $pro = $prevPro;
                } else {
                    $pro = new Pro();
                }
        
                $pro->setMusician($musician);
                $pro->setStarted($started);
                $pro->setEnding($ending);
                $entityManager->persist($pro);
                $entityManager->flush();
                $email = $musician->getRealEmail();
                $data = [];
        
                $musician->setAccount($membership);
                $entityManager->persist($musician);
                $entityManager->flush();

                if($membershipManager->sendMembershipConfirmation($email, $membership, $musician->getUsername())){
                    // $this->addFlash('success', 'Notification mail was sent successfully');
                } 

                $msg = "Payment successful! You have successfully activated PRO version. Click here to go to your profile page";
                $ret = ['status' => 'success', 'msg' => $msg];
                return new JsonResponse($ret);
    
            } else {

                $msg = $status->ResultDesc;
                $errors['error'] = $msg;
                $ret = ['status' => 'failed', 'errors' => $errors];
                return new JsonResponse($ret);

            }
        }

        // return new JsonResponse($status);

    }

    public function curl_call($path){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /**
     * @Route("/touch/script/pmt", name="callback")
     */
    public function callBack() {
               
        if($json = json_decode(file_get_contents("php://input"), true)) {
            $CheckoutRequestID = $this->getVar($json, 'CheckoutRequestID', 2);
            $entityManager = $this->getDoctrine()->getManager();
            $callback = new Callback();
            $callback->setCallbackmetadata($json);
            $callback->setCheckoutRequestID($CheckoutRequestID);
            $entityManager->persist($callback);
            $entityManager->flush();        
            // $this->followUp($CheckoutRequestID);
        } else {
            $json = $_POST;
            $CheckoutRequestID = $this->getVar($json, 'CheckoutRequestID', 2);
            $entityManager = $this->getDoctrine()->getManager();
            $callback = new Callback();
            $callback->setCallbackmetadata($json);
            $callback->setCheckoutRequestID($CheckoutRequestID);
            $entityManager->persist($callback);
            $entityManager->flush();        
            // $this->followUp($CheckoutRequestID);
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

    /**
     * @Route("/touch/follow/up", name="follow_up")
     */
    public function followUp($CheckoutRequestID){
        $entityManager = $this->getDoctrine()->getManager();
        $callback = $entityManager->getRepository('App:Callback')->findOneByCheckoutRequestID($CheckoutRequestID);

        $json = $callback->getCallbackmetadata();

        $Amount = $this->getVar($json, 'Amount', 3);
        $MpesaReceiptNumber = $this->getVar($json, 'MpesaReceiptNumber', 3);
        $TransactionDate = $this->getVar($json, 'TransactionDate', 3);
        $PhoneNumber = $this->getVar($json, 'PhoneNumber', 3);
    
        $callback->setMpesaReceiptNumber($MpesaReceiptNumber);
        $callback->setTransactionDate($TransactionDate);
        $callback->setAmount($Amount);
        $callback->setPhoneNumber($PhoneNumber);
        $entityManager->persist($callback);
        $entityManager->flush();        

        return new JsonResponse('true');

    }

    public function endTransaction() {

        $mpesa= new \Safaricom\Mpesa\Mpesa();

        $callbackData=$mpesa->finishTransaction();


    }

    public function formatPhoneNumber($phone){

        //The default country code if the recipient's is unknown:
        $country_code  = '254';

        //Remove any parentheses and the numbers they contain:
        $phone = preg_replace("/\([0-9]+?\)/", "", $phone);

        //Strip spaces and non-numeric characters:
        $phone = preg_replace("/[^0-9]/", "", $phone);

        //Strip out leading zeros:
        $phone = ltrim($phone, '0');

        //Look up the country dialling code for this number:
        $pfx = $country_code;

        //Check if the number doesn't already start with the correct dialling code:
        if ( !preg_match('/^'.$pfx.'/', $phone)  ) {
            $phone = $pfx.$phone;
        }

        //return the converted number:
        return $phone;        

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
