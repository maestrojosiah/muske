<?php

namespace App\Controller;

use App\Entity\JobToBeOffered;
use App\Form\JobToBeOfferedType;
use App\Repository\JobToBeOfferedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/job/to/be/offered")
 */
class JobToBeOfferedController extends AbstractController
{
    /**
     * @Route("/", name="job_to_be_offered_index", methods={"GET"})
     */
    public function index(JobToBeOfferedRepository $jobToBeOfferedRepository): Response
    {
        return $this->render('job_to_be_offered/index.html.twig', [
            'job_to_be_offereds' => $jobToBeOfferedRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="job_to_be_offered_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $jobToBeOffered = new JobToBeOffered();
        $form = $this->createForm(JobToBeOfferedType::class, $jobToBeOffered);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($jobToBeOffered);
            $entityManager->flush();

            return $this->redirectToRoute('job_to_be_offered_index');
        }

        return $this->render('job_to_be_offered/new.html.twig', [
            'job_to_be_offered' => $jobToBeOffered,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="job_to_be_offered_show", methods={"GET"})
     */
    public function show(JobToBeOffered $jobToBeOffered): Response
    {
        return $this->render('job_to_be_offered/show.html.twig', [
            'job_to_be_offered' => $jobToBeOffered,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="job_to_be_offered_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, JobToBeOffered $jobToBeOffered): Response
    {
        $form = $this->createForm(JobToBeOfferedType::class, $jobToBeOffered);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('job_to_be_offered_index');
        }

        return $this->render('job_to_be_offered/edit.html.twig', [
            'job_to_be_offered' => $jobToBeOffered,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="job_to_be_offered_delete", methods={"DELETE"})
     */
    public function delete(Request $request, JobToBeOffered $jobToBeOffered): Response
    {
        if ($this->isCsrfTokenValid('delete'.$jobToBeOffered->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($jobToBeOffered);
            $entityManager->flush();
        }

        return $this->redirectToRoute('job_to_be_offered_index');
    }
}
