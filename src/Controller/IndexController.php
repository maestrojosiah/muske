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
     * @Route("/search/music-service", name="search")
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
     * @Route("/register/choose-theme", name="choose_theme")
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
        $string = '{
            "Body":{"stkCallback":
                {
                    "MerchantRequestID":"28145-1122988-1",
                    "CheckoutRequestID":"ws_CO_030720201559453712",
                    "ResultCode":0,
                    "ResultDesc":"The service request is processed successfully.",
                    "CallbackMetadata":{
                        "Item":[{
                            "Name":"Amount",
                            "Value":1
                        },
                        {
                            "Name":"MpesaReceiptNumber",
                            "Value":"OG38G7S0TI"
                        },
                        {
                            "Name":"Balance"
                        },
                        {
                            "Name":"TransactionDate","Value":20200703160003
                        },
                        {
                            "Name":"PhoneNumber","Value":254705285959
                        }]
                    }
                }
            }
        }';
        
        $MerchantRequestID = $this->getVar($string, 'MerchantRequestID', 2);
        $CheckoutRequestID = $this->getVar($string, 'CheckoutRequestID', 2);
        $ResultCode = $this->getVar($string, 'ResultCode', 2);
        $ResultDesc = $this->getVar($string, 'ResultDesc', 2);
        $CallbackMetadata = $this->getVar($string, 'CallbackMetadata', 2);
        $Amount = $this->getVar($string, 'Amount', 3);
        $MpesaReceiptNumber = $this->getVar($string, 'MpesaReceiptNumber', 3);
        $TransactionDate = $this->getVar($string, 'TransactionDate', 3);
        $PhoneNumber = $this->getVar($string, 'PhoneNumber', 3);

        return $this->render('index/test.html.twig', [
            'test' => $PhoneNumber,
        ]);
    }

    function getVar($hay, $needle, $level){
        // decode the json to array
        $decoded = json_decode($hay, true);

        // body and stkCallback
        $body = $decoded['Body'];
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
}
