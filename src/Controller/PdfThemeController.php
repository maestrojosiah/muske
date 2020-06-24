<?php

namespace App\Controller;

use App\Entity\PdfTheme;
use App\Form\PdfThemeType;
use App\Repository\PdfThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/pdf/download/theme")
 */
class PdfThemeController extends AbstractController
{
    /**
     * @Route("/", name="pdf_theme_index", methods={"GET"})
     */
    public function index(PdfThemeRepository $pdfThemeRepository): Response
    {
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $musician = $this->getUser();
        return $this->render('pdf_theme/index.html.twig', [
            'pdf_themes' => $pdfThemeRepository->findAll(),
            'musician' => $musician,
        ]);
    }

    /**
     * @Route("/new", name="pdf_theme_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $pdfTheme = new PdfTheme();
        $form = $this->createForm(PdfThemeType::class, $pdfTheme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pdfTheme);
            $entityManager->flush();

            return $this->redirectToRoute('pdf_theme_index');
        }

        return $this->render('pdf_theme/new.html.twig', [
            'pdf_theme' => $pdfTheme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pdf_theme_show", methods={"GET"})
     */
    public function show(PdfTheme $pdfTheme): Response
    {
        return $this->render('pdf_theme/show.html.twig', [
            'pdf_theme' => $pdfTheme,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pdf_theme_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PdfTheme $pdfTheme): Response
    {
        $form = $this->createForm(PdfThemeType::class, $pdfTheme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pdf_theme_index');
        }

        return $this->render('pdf_theme/edit.html.twig', [
            'pdf_theme' => $pdfTheme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pdf_theme_delete", methods={"DELETE"})
     */
    public function delete(Request $request, PdfTheme $pdfTheme): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pdfTheme->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($pdfTheme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('pdf_theme_index');
    }
}
