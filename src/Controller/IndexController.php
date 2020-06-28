<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\MusicianRepository;
use App\Repository\JobToBeOfferedRepository;
use App\Repository\SkillRepository;
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
    public function search(SeoPageInterface $seoPage, JobToBeOfferedRepository $jobToBeOfferedRepository, MusicianRepository $musicianRepository, SkillRepository $skillRepository): Response
    {
        $skills = $skillRepository->findSkillsList();
        $specialties = $jobToBeOfferedRepository->findSpecialtiesList();
        $titles = $musicianRepository->findTitlesList();
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

        return $this->render('email/account_type.html.twig', [
            'username' => 'test',
            'membership' => 'test'
        ]);
    }

}
