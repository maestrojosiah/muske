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
        $musician = $this->getUser();
        $skills = $this->getDoctrine()->getManager()->getRepository('App:Skill')->findall();
        $job_roles = $this->getDoctrine()->getManager()->getRepository('App:JobToBeOffered')->findall();
        return $this->render('musician/index.html.twig', [
            'musician' => $musician,
            'skills' => $skills,
            'job_roles' => $job_roles,
            'musicians' => $musicianRepository->findAll(),
        ]);
    }

    /**
     * @Route("/musician/details", name="musician_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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

        $skills_auto_fill = $this->getDoctrine()->getManager()->getRepository('App:Skill')->findall();
        $job_roles_auto_fill = $this->getDoctrine()->getManager()->getRepository('App:JobToBeOffered')->findall();

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
            'skills_auto_fill' => $skills_auto_fill,
            'job_roles_auto_fill' => $job_roles_auto_fill,
            'filter' => $filter,
        ]);
    }

    /**
     * @Route("/musician/finalize", name="finalize_musician", methods={"GET","POST"})
     */
    public function finalize(Request $request, SluggerInterface $slugger)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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
                    $current_photo_thumb_path = $this->getParameter('brochures_directory')."/thumbs/".$musician->getPhoto();
                    unlink($current_photo_path);
                    unlink($current_photo_thumb_path);
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

                $updir = $this->getParameter('brochures_directory');
                $musician = $this->getUser();
                // $img = $this->getParameter('brochures_directory')."/".$musician->getPhoto();
                $img = $musician->getPhoto();
                $this->makeThumbnails($updir, $img);
            }

            // ... persist the $product variable or any other work
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($musician);
            $entityManager->flush();

            // return $this->redirect($this->generateUrl('musician_show', ['username' => $musician->getUsername()]));
            return $this->redirect($this->generateUrl('musician_profile'));
        }

        // define email phone age and full name
        $musician_photo = $musician->getPhoto();
        $musician_about = $musician->getAbout();
        $musician_projects = $musician->getProjects()[0];

        $details_array = [$musician_photo, $musician_about, $musician_projects];
        
        // to know if all the above have content                            
        $filter = array_filter($details_array, function($k) {
            return (isset($k) || !empty($k));
        }, ARRAY_FILTER_USE_BOTH );


        //if the details above are in database, then move to add skills
        if (count($filter) >= 2) {
            // come back here and check more things 
            // return $this->redirectToRoute('musician_show', ['username' => $musician]);
            return $this->redirectToRoute('musician_profile');

        }
        

        return $this->render('musician/final.html.twig', [
            'musician' => $musician,
            'filter' => $filter,
            'form' => $form->createView(),
        ]);
    }

        /**
     * @Route("/musician/final/edit", name="finalize_musician_edit", methods={"GET","POST"})
     */
    public function finalize_edit(Request $request, SluggerInterface $slugger)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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
                    $current_photo_thumb_path = $this->getParameter('brochures_directory')."/thumbs/".$musician->getPhoto();
                    unlink($current_photo_thumb_path);

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
                $updir = $this->getParameter('brochures_directory');
                $musician = $this->getUser();
                // $img = $this->getParameter('brochures_directory')."/".$musician->getPhoto();
                $img = $musician->getPhoto();
                $this->makeThumbnails($updir, $img);

            }

            // ... persist the $product variable or any other work
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($musician);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('musician_show', ['username' => $musician->getUsername()]));
        }


        return $this->render('musician/final_edit.html.twig', [
            'musician' => $musician,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{username}", name="musician_show", methods={"GET"})
     */
    public function show($username): Response
    {

        $musician = $this->getDoctrine()->getManager()->getRepository('App:Musician')->findByUsername($username)[0];

        if(count($musician->getUploadedphotos()) >= 4){
            $fourPhotos =  $this->getDoctrine()->getManager()->getRepository('App:Gallery')
            ->findFourPhotos($musician);
        } else {
            $fourPhotos = [];
        }
        if($musician->getSettings()){
            $jobs = $this->getDoctrine()->getManager()->getRepository('App:Job')
            ->findByGivenField($musician->getSettings()->getJobOrder(), 
                $musician->getSettings()->getJobOrderBy(), $musician);

            $educ = $this->getDoctrine()->getManager()->getRepository('App:Education')
            ->findByGivenField($musician->getSettings()->getEduOrder(), 
                $musician->getSettings()->getEduOrderBy(), $musician);

        } else {
            $jobs = $musician->getJobs();
            $educ = $musician->getEducation();
        }
        return $this->render('musician/show.html.twig', [
            'musician' => $musician,
            'jobs' => $jobs,
            'educ' => $educ,
            'fourPhotos' => $fourPhotos,
        ]);
    }

    /**
     * @Route("/musician/profile", name="musician_profile", methods={"GET"})
     */
    public function profile(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $musician = $this->getUser();
        $data = [];
        if($musician->getSettings()){
            if($musician->getSettings()->getMuske() == 'true'){
                $membership = "Muske";
            } elseif($musician->getSettings()->getPro() == 'true'){
                $membership = "Pro";
            } else {
                $membership = "Basic";
            }

            $details_array = [$musician->getSettings()->getOnline(), $musician->getSettings()->getTsc(), $musician->getSettings()->getPlaceofwork()];
            
            // to know if all the above have content                            
            $filter = array_filter($details_array, function($k) {
                return (isset($k) || !empty($k));
            }, ARRAY_FILTER_USE_BOTH );

            if (count($filter) < 3) {
                $data['complete'] = false;
            } else {
                $data['complete'] = true;
            }
        
        } else {
            $membership = "basic";
            $data['complete'] = false;
        }

        $roles_array = [];
        $jobs_array = $this->getDoctrine()->getManager()->getRepository('App:Job')
            ->findByGivenField($musician->getSettings()->getJobOrder(), 
                $musician->getSettings()->getJobOrderBy(), $musician);
        foreach ($jobs_array as $key => $job) {
            if(count($job->getRoles()) > 0){
                $roles_array[$key] = $job->getRoles();
            }
            
        }

        $skills_array = [];
        $edu_array = $this->getDoctrine()->getManager()->getRepository('App:Education')
            ->findByGivenField($musician->getSettings()->getEduOrder(), 
            $musician->getSettings()->getEduOrderBy(), $musician);
        foreach ($edu_array as $key => $edu) {
            if(count($edu->getSpecialties()) > 0){
                $skills_array[$key] = $edu->getSpecialties();
            }
            
        }

        return $this->render('musician/profile.html.twig', [
            'musician' => $musician,
            'data' => $data,
            'membership' => $membership,
            'roles' => $roles_array,
            'skills' => $skills_array,
        ]);
    }


    /**
     * @Route("/musician/plan", name="musician_plan", methods={"GET"})
     */
    public function plan(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $musician = $this->getUser();

        if($musician->getSettings()->getMuske() == 'true'){
            $membership = "Muske";
        } elseif($musician->getSettings()->getPro() == 'true'){
            $membership = "Pro";
        } else {
            $membership = "Basic";
        }
        
        return $this->render('musician/plan.html.twig', [
            'musician' => $musician,
            'membership' => $membership,
        ]);
    }

    /**
     * @Route("/musician/plan_details/{plan}", name="musician_plan_details", methods={"GET"})
     */
    public function planDetails($plan): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $musician = $this->getUser();

        if($musician->getSettings()->getMuske() == 'true'){
            $membership = "Muske";
        } elseif($musician->getSettings()->getPro() == 'true'){
            $membership = "Pro";
        } else {
            $membership = "Basic";
        }
        
        return $this->render('musician/plan_details.html.twig', [
            'musician' => $musician,
            'plan' => $plan,
            'membership' => $membership,
        ]);
    }


    /**
     * @Route("/musician/{username}/edit", name="musician_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Musician $musician): Response
    {
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // for placeholding if data exists

        // define email phone age and full name
        $musician_email = $musician->getEmail();
        $musician_phone = $musician->getPhone();
        $musician_age = $musician->getAge();
        $musician_fullname = $musician->getFullname();
        $skills = $musician->getSkills();
        $education = $musician->getEducation();
        $jobs = $musician->getJobs();
        $roles = $musician->getJobstobeoffered();
        $salary = $musician->getCurrentsalary();
        $salary_exp = $musician->getExpectedSalary();

        $skills_auto_fill = $this->getDoctrine()->getManager()->getRepository('App:Skill')->findall();
        $job_roles_auto_fill = $this->getDoctrine()->getManager()->getRepository('App:JobToBeOffered')->findall();

        $fields = ["email" => $musician_email,
                    "phone" => $musician_phone,
                    "age" => $musician_age,
                    "full_name" => $musician_fullname,
                    "salary" => $salary,
                    "exp_salary" => $salary_exp,
                    "skills" => $skills,
                    "education" => $education,
                    "jobs" => $jobs,
                    "roles" => $roles
                ];

        return $this->render('musician/edit.html.twig', [
            'musician' => $musician,
            'fields' => $fields,
            'skills_auto_fill' => $skills_auto_fill,
            'job_roles_auto_fill' => $job_roles_auto_fill,
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


    public function makeThumbnails($updir, $img)
    {

        $thumbnail_width = 300;
        $thumbnail_height = 300;
        $thumb_beforeword = "thumbs";
        $arr_image_details = getimagesize("$updir" .  '/'. "$img"); // pass id to thumb name
        $original_width = $arr_image_details[0];
        $original_height = $arr_image_details[1];
        if ($original_width > $original_height) {
            $new_width = $thumbnail_width;
            $new_height = intval($original_height * $new_width / $original_width);
        } else {
            $new_height = $thumbnail_height;
            $new_width = intval($original_width * $new_height / $original_height);
        }
        $dest_x = intval(($thumbnail_width - $new_width) / 2);
        $dest_y = intval(($thumbnail_height - $new_height) / 2);
        if ($arr_image_details[2] == IMAGETYPE_GIF) {
            $imgt = "ImageGIF";
            $imgcreatefrom = "ImageCreateFromGIF";
        }
        if ($arr_image_details[2] == IMAGETYPE_JPEG) {
            $imgt = "ImageJPEG";
            $imgcreatefrom = "ImageCreateFromJPEG";
        }
        if ($arr_image_details[2] == IMAGETYPE_PNG) {
            $imgt = "ImagePNG";
            $imgcreatefrom = "ImageCreateFromPNG";
        }
        if ($imgt) {
            $old_image = $imgcreatefrom("$updir" .  '/'. "$img");
            $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
            imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
            $imgt($new_image, "$updir" .  '/'. "$thumb_beforeword/" . "$img");
        }

        return $imgt;

    }


}
