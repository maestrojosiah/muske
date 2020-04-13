<?php

namespace App\Controller;

use App\Entity\Musician;
use App\Form\MusicianType;
use App\Repository\MusicianRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;


class MusicianController extends AbstractController
{
    /**
     * @Route("/musician/index", name="musician_index", methods={"GET"})
     */
    public function index(MusicianRepository $musicianRepository): Response
    {
        return $this->render('musician/index.html.twig', [
            'musicians' => $musicianRepository->findAll(),
        ]);
    }

    /**
     * @Route("/musician/details", name="musician_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        //this is for displaying the page and redirecting if all details are intact
        //get the musician in session
        $musician = $this->getUser();
        
        // for placeholding if data exists

        // define email phone age and full name
        $musician_email = $musician->getEmail();
        $musician_phone = $musician->getPhone();
        $musician_age = $musician->getAge();
        $musician_fullname = $musician->getFullname();
        $skills = $musician->getSkills()[0];
        $education = $musician->getEducation()[0];
        $jobs = $musician->getJobs()[0];
        $roles = $musician->getJobstobeoffered()[0];
        $salary = $musician->getCurrentsalary();
        $salary_exp = $musician->getExpectedSalary();

        $details_array = [$musician_email, $musician_phone, $musician_age, $musician_fullname, $skills,
                            $education, $jobs, $roles, $salary, $salary_exp];
        $fields = ["email" => $musician_email,
                    "phone" => $musician_phone,
                    "age" => $musician_age,
                    "full_name" => $musician_fullname,
                    "salary" => $salary,
                    "exp_salary" => $salary_exp,
                    "skills" => $musician->getSkills(),
                    "education" => $musician->getEducation(),
                    "jobs" => $musician->getJobs(),
                    "roles" => $musician->getJobstobeoffered()
                ];
        
        // to know if all the above have content                            
        $filter = array_filter($details_array, function($k) {
            return (isset($k) || !empty($k));
        }, ARRAY_FILTER_USE_BOTH );


        //if the details above are in database, then move to add skills
        if (count($filter) == 10) {
            // come back here and check more things 
            return $this->redirectToRoute('finalize_musician');

        }

        return $this->render('musician/new.html.twig', [
            'musician' => $musician,
            'fields' => $fields,
            'filter' => $filter,
        ]);
    }

    /**
     * @Route("/musician/finalize", name="finalize_musician", methods={"GET","POST"})
     */
    public function finalize(Request $request, SluggerInterface $slugger)
    {
        $musician = $this->getUser();

        $form = $this->createForm(MusicianType::class, $musician);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photo')->getData();

            // this condition is needed because the 'photo' field is not required
            // so the image file must be processed only when a file is uploaded
            if ($photoFile) {
                //delete the current phowo if available
                if($musician->getPhoto() != null ) {
                    $current_photo_path = $this->getParameter('brochures_directory')."/".$musician->getPhoto();
                    unlink($current_photo_path);
                }
               
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photoFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'photoFilename' property to store the PDF file name
                // instead of its contents
                $musician->setPhoto($newFilename);
            }

            // ... persist the $product variable or any other work
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($musician);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('musician_show', ['username' => $musician->getUsername()]));
        }


        return $this->render('musician/final.html.twig', [
            'musician' => $musician,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{username}", name="musician_show", methods={"GET"})
     */
    public function show(Musician $musician): Response
    {
        return $this->render('musician/show.html.twig', [
            'musician' => $musician,
        ]);
    }

    /**
     * @Route("/musician/{id}/edit", name="musician_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Musician $musician): Response
    {
        $form = $this->createForm(MusicianType::class, $musician);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('musician_index');
        }

        return $this->render('musician/edit.html.twig', [
            'musician' => $musician,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/musician/{id}", name="musician_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Musician $musician): Response
    {
        if ($this->isCsrfTokenValid('delete'.$musician->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($musician);
            $entityManager->flush();
        }

        return $this->redirectToRoute('musician_index');
    }


}
