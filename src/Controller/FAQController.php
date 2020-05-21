<?php

namespace App\Controller;

use App\Entity\FAQ;
use App\Form\FAQType;
use App\Repository\FAQRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FaqCategoryRepository;

/**
 * @Route("/f/a/q")
 */
class FAQController extends AbstractController
{
    /**
     * @Route("/", name="f_a_q_index", methods={"GET"})
     */
    public function index(FAQRepository $fAQRepository, FaqCategoryRepository $faqCategoryRepository): Response
    {
        $categories = $faqCategoryRepository->findAll();
        return $this->render('faq/index.html.twig', [
            'categories' => $categories,
            'faqs' => $fAQRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="f_a_q_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $fAQ = new FAQ();
        $form = $this->createForm(FAQType::class, $fAQ);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fAQ);
            $entityManager->flush();

            return $this->redirectToRoute('f_a_q_index');
        }

        return $this->render('faq/new.html.twig', [
            'f_a_q' => $fAQ,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="f_a_q_show", methods={"GET"})
     */
    public function show(FAQ $fAQ): Response
    {
        return $this->render('faq/show.html.twig', [
            'f_a_q' => $fAQ,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="f_a_q_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, FAQ $fAQ): Response
    {
        $form = $this->createForm(FAQType::class, $fAQ);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('f_a_q_index');
        }

        return $this->render('faq/edit.html.twig', [
            'f_a_q' => $fAQ,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="f_a_q_delete", methods={"DELETE"})
     */
    public function delete(Request $request, FAQ $fAQ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fAQ->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($fAQ);
            $entityManager->flush();
        }

        return $this->redirectToRoute('f_a_q_index');
    }
}
