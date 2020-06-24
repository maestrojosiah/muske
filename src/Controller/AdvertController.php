<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Form\AdvertType;
use App\Repository\AdvertRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MusicianRepository;

/**
 * @Route("/advert")
 */
class AdvertController extends AbstractController
{
    /**
     * @Route("/view/{id}", name="advert_index", methods={"GET"})
     */
    public function index(AdvertRepository $advertRepository, $id = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $adverts = $advertRepository->findAll();
        if(null !== $id){
            $advert = $advertRepository->find($id);
        } else {
            $advert = null;
        }
        

        return $this->render('advert/index.html.twig', [
            'adverts' => $adverts,
            'advert' => $advert,
        ]);
    }

    /**
     * @Route("/new", name="advert_new", methods={"GET","POST"})
     */
    public function new(MusicianRepository $musicianRepository, Request $request): Response
    {
        $continue = false;
        $counter = 3;
        $proMusicians = $musicianRepository->getMusicians('pro',$counter);
        if(null !== $proMusicians){
            $countProMusicians = count($proMusicians);
            if($countProMusicians < $counter){
                $counter = $counter - $countProMusicians;
                $continue = true;
            }    
        }
        if($continue == true){
            $muskeMusicians = $musicianRepository->getMusicians('muske',$counter);
            if(null !== $muskeMusicians){
                $countMuskeMusicians = count($muskeMusicians);
                if($countMuskeMusicians < $counter){
                    $counter = $counter - $countMuskeMusicians;
                    $continue = true;
                }
            }
        } else {
            $muskeMusicians = null;
        }
        if($continue == true){
            $basicMusicians = $musicianRepository->getMusicians('basic',$counter);
        } else {
            $basicMusicians = null;
        }
        
        return $this->render('advert/new.html.twig', [
            'proMusicians' => $proMusicians,
            'muskeMusicians' => $muskeMusicians,
            'basicMusicians' => $basicMusicians,
        ]);
    }

    /**
     * @Route("/track/{phone}/{code}", name="advert_tracking", methods={"GET"})
     */
    public function track(Request $request, AdvertRepository $advertRepository, $phone, $code): Response
    {
        $advert = $advertRepository->findOneBy(
            ['phone' => $phone, 'code' => $code],
            ['id' => 'ASC']
        );

        return $this->render('advert/tracking.html.twig', [
            'advert' => $advert,
        ]);
    }

    /**
     * @Route("/{id}", name="advert_show", methods={"GET"})
     */
    public function show(Advert $advert): Response
    {
        return $this->render('advert/show.html.twig', [
            'advert' => $advert,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="advert_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Advert $advert): Response
    {
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('advert_index');
        }

        return $this->render('advert/edit.html.twig', [
            'advert' => $advert,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="advert_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Advert $advert): Response
    {
        if ($this->isCsrfTokenValid('delete'.$advert->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($advert);
            $entityManager->flush();
        }

        return $this->redirectToRoute('advert_index');
    }
}
