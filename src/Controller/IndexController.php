<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\MusicianRepository;
use App\Repository\SkillRepository;
use App\Repository\PdfThemeRepository;

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
    public function search(MusicianRepository $musicianRepository, SkillRepository $skillRepository): Response
    {
        $skills = $skillRepository->findSkillsList();
        $musicians = $musicianRepository->findAll();
        $proMusicians = $musicianRepository->getMusicians('pro');
        $muskeMusicians = $musicianRepository->getMusicians('muske');
        $basicMusicians = $musicianRepository->getMusicians('basic');
        return $this->render('index/search.html.twig', [
            'skills' => $skills,
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

        $themes = $pdfThemeRepository -> findFive();
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

        return $this->render('index/test.html.twig');
    }

}
