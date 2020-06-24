<?php

namespace App\Controller;

use App\Entity\WebTheme;
use App\Form\WebThemeType;
use App\Repository\WebThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/web/online/theme")
 */
class WebThemeController extends AbstractController
{
    /**
     * @Route("/", name="web_theme_index", methods={"GET"})
     */
    public function index(WebThemeRepository $webThemeRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $musician = $this->getUser();
        return $this->render('web_theme/index.html.twig', [
            'web_themes' => $webThemeRepository->findAll(),
            'musician' => $musician,
        ]);
    }

    /**
     * @Route("/new", name="web_theme_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $webTheme = new WebTheme();
        $form = $this->createForm(WebThemeType::class, $webTheme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($webTheme);
            $entityManager->flush();

            return $this->redirectToRoute('web_theme_index');
        }

        return $this->render('web_theme/new.html.twig', [
            'web_theme' => $webTheme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="web_theme_show", methods={"GET"})
     */
    public function show(WebTheme $webTheme): Response
    {
        return $this->render('web_theme/show.html.twig', [
            'web_theme' => $webTheme,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="web_theme_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, WebTheme $webTheme): Response
    {
        $form = $this->createForm(WebThemeType::class, $webTheme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('web_theme_index');
        }

        return $this->render('web_theme/edit.html.twig', [
            'web_theme' => $webTheme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="web_theme_delete", methods={"DELETE"})
     */
    public function delete(Request $request, WebTheme $webTheme): Response
    {
        if ($this->isCsrfTokenValid('delete'.$webTheme->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($webTheme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('web_theme_index');
    }
}
