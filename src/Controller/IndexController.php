<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
    //     if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
    //         $musician = $this->getUser();
    //         return $this->redirectToRoute('musician_show', ['username' => $musician]);
    //    }

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'last_username' => $lastUsername, 
            'error' => $error,
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
