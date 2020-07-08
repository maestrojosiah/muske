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
use Sonata\SeoBundle\Seo\SeoPageInterface;

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
        $musician = $this->getUser();
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $adverts = $advertRepository->findAll();
        if(null !== $id){
            $advert = $advertRepository->find($id);
        } else {
            $advert = null;
        }
        
        $notifs = [];
        $notifs[] = count($musician->getJobs()) < 1 ? ['musician_new' => 'Please add job history to your resume'] : [];
        $notifs[] = count($musician->getEducation()) < 1 ? ['musician_new' => 'Please add education background to your resume'] : [];
        $notifs[] = count($musician->getReferees()) < 1 ? ['musician_show' => 'You may need to add referees to your resume'] : [];

        return $this->render('advert/index.html.twig', [
            'adverts' => $adverts,
            'advert' => $advert,
            'notifs' => array_filter($notifs),
        ]);
    }

    /**
     * @Route("/create/new/music-job/advert", name="advert_new", methods={"GET","POST"})
     */
    public function new(SeoPageInterface $seoPage, MusicianRepository $musicianRepository, Request $request): Response
    {

        $seoPage
        ->setTitle("Advertise a music job position to musicians in Kenya - 2020")
        ->addMeta('name', 'keywords', "Advertise music job choir trainer pianist church pianist music teacher ")
        ->addMeta('name', 'description', "Advertize a music job and have it reach all the musicians in the MuSKe platform. Track the performance of your advert in real time!")
        ->addMeta('property', 'og:title', "Advertise a music job position to musicians in Kenya - 2020")
        ->addMeta('property', 'og:url',  $this->generateUrl('advert_new'))
        ->addMeta('property', 'og:description', "Advertize a music job and have it reach all the musicians in the MuSKe platform. Track the performance of your advert in real time!")
        ;

        $continue = false;
        $counter = 3;
        $proMusicians = $musicianRepository->getMusicians('pro',$counter);
        if(null !== $proMusicians){
            $countProMusicians = count($proMusicians);
            if($countProMusicians <= $counter){
                $counter = $counter - $countProMusicians;
            }    
        }
        $counter < 1 ? $continue = false : $continue = true;
        if($continue == true){
            $muskeMusicians = $musicianRepository->getMusicians('muske',$counter);
            if(null !== $muskeMusicians){
                $countMuskeMusicians = count($muskeMusicians);
                if($countMuskeMusicians <= $counter){
                    $counter = $counter - $countMuskeMusicians;
                }
            }
        } else {
            $muskeMusicians = null;
        }
        $counter < 1 ? $continue = false : $continue = true;
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
