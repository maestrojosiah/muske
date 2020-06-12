<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\MusicianRepository;
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
    public function search(MusicianRepository $mucisianRepository): Response
    {
        $musicians = $mucisianRepository->findAll();
        return $this->render('index/search.html.twig', [
            'musicians' => $musicians,
        ]);
    }

    /**
     * @Route("/choose-theme", name="choose_theme")
     */
    public function themes(PdfThemeRepository $pdfThemeRepository): Response
    {
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

}
