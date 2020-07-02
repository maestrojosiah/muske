<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\MusicianRepository;
use App\Repository\JobToBeOfferedRepository;
use App\Repository\SkillRepository;
use App\Repository\SettingsRepository;
use App\Repository\PdfThemeRepository;
use Sonata\SeoBundle\Seo\SeoPageInterface;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(SeoPageInterface $seoPage, JobToBeOfferedRepository $jobToBeOfferedRepository, MusicianRepository $musicianRepository, SkillRepository $skillRepository, SettingsRepository $settingsRepository): Response
    {
        $skills = $skillRepository->findSkillsList();
        $specialties = $jobToBeOfferedRepository->findSpecialtiesList();
        $titles = $musicianRepository->findTitlesList();
        $locations = $settingsRepository->findLocationList();
        $musicians = $musicianRepository->findAll();
        $proMusicians = $musicianRepository->getMusicians('pro');
        $muskeMusicians = $musicianRepository->getMusicians('muske');
        $basicMusicians = $musicianRepository->getMusicians('basic');
        $skills_list = [];
        foreach ($skills as $skill ) {
            $skills_list[] = $skill['skillname'];
        }
        $skill_list = implode(" ", $skills_list);

        $seoPage
        ->setTitle("Search for music services")
        ->addMeta('name', 'keywords', $skill_list)
        ->addMeta('name', 'description', "Search for a music instructor in any music field from the country's largest musician database")
        ->addMeta('property', 'og:title', "Search for music services")
        ->addMeta('property', 'og:url',  $this->generateUrl('search'))
        ->addMeta('property', 'og:description', "Search for a music instructor in any music field from the country's largest musician database")
    ;

        return $this->render('index/search.html.twig', [
            'skills' => $skills,
            'specialties' => $specialties,
            'titles' => $titles,
            'locations' => $locations,
            'musicians' => $musicians,
            'pro_musicians' => $proMusicians,
            'muske_musicians' => $muskeMusicians,
            'basic_musicians' => $basicMusicians
        ]);
    }

    /**
     * @Route("/choose-theme", name="choose_theme")
     */
    public function themes(PdfThemeRepository $pdfThemeRepository): Response
    {
        
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $musician = $this->getUser();
            return $this->redirectToRoute('musician_profile');
        }

        $themes = $pdfThemeRepository -> findAll();
        return $this->render('index/choose_theme.html.twig', [
            'themes' => $themes,
        ]);
    }

    /**
     * @Route("/get/error/and/display/{error_msg}", name="error")
     */
    public function error($error_msg = ""): Response
    {

        return $this->render('index/error.html.twig', [
            'error' => $error_msg,
        ]);
    }

    /**
     * @Route("/test", name="test")
     */
    public function test(): Response
    {

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

        $BusinessShortCode = "174379";
        $LipaNaMpesaPasskey = "MTc0Mzc5YmZiMjc5ZjlhYTliZGJjZjE1OGU5N2RkNzFhNDY3Y2QyZTBjODkzMDU5YjEwZjc4ZTZiNzJhZGExZWQyYzkxOTIwMTgwNDA5MDkzMDAy";
        $Timestamp = "20180409093002";
        $TransactionType = "CustomerBuyGoodsOnline";
        $Amount = "5";
        $PartyA = "254708374149";
        $PartyB = "174379";
        $PhoneNumber = "254708374149";
        $CallBackURL = "https://sandbox.safaricom.co.ke/mpesa/";
        $AccountReference = "account";
        $TransactionDesc = "test";
        $Remarks = "pro";
    
      
        $mpesa= new \Safaricom\Mpesa\Mpesa();

        $stkPushSimulation = $mpesa->STKPushSimulation($BusinessShortCode, $LipaNaMpesaPasskey, $TransactionType, $Amount, $PartyA, $PartyB, $PhoneNumber, $CallBackURL, $AccountReference, $TransactionDesc, $Remarks);

        
        return $this->render('index/test.html.twig', [
            'test' => $stkPushSimulation,
        ]);
    }

}
