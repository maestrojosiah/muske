<?php

namespace App\Controller;

use App\Entity\FaqCategory;
use App\Form\FaqCategoryType;
use App\Repository\FaqCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/faq/category")
 */
class FaqCategoryController extends AbstractController
{
    /**
     * @Route("/", name="faq_category_index", methods={"GET"})
     */
    public function index(FaqCategoryRepository $faqCategoryRepository): Response
    {
        return $this->render('faq_category/index.html.twig', [
            'faq_categories' => $faqCategoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="faq_category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $faqCategory = new FaqCategory();
        $form = $this->createForm(FaqCategoryType::class, $faqCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($faqCategory);
            $entityManager->flush();

            return $this->redirectToRoute('faq_category_index');
        }

        return $this->render('faq_category/new.html.twig', [
            'faq_category' => $faqCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="faq_category_show", methods={"GET"})
     */
    public function show(FaqCategory $faqCategory): Response
    {
        return $this->render('faq_category/show.html.twig', [
            'faq_category' => $faqCategory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="faq_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, FaqCategory $faqCategory): Response
    {
        $form = $this->createForm(FaqCategoryType::class, $faqCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('faq_category_index');
        }

        return $this->render('faq_category/edit.html.twig', [
            'faq_category' => $faqCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="faq_category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, FaqCategory $faqCategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$faqCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($faqCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('faq_category_index');
    }
}
