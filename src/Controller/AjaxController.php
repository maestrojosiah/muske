<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Skill;
use App\Entity\Education;
use App\Entity\Job;
use App\Entity\JobToBeOffered;


class AjaxController extends AbstractController
{
    /**
     * @Route("/ajax", name="ajax")
     */
    public function index()
    {
        return $this->render('ajax/index.html.twig', [
            'controller_name' => 'AjaxController',
        ]);
    }

    /**
     * @Route("/more_info", name="update_user")
     */
    public function updateMusician(Request $request)
    {
        if($request->request->get('email')){
            // $quantityValue = $request->request->get('quantity') ? $request->request->get('quantity') : 1 ;
            // $fullId = explode('_',$request->request->get('some_var_name'));
            // $id = $fullId[1];
            $email = $this->sanitizeInput($request->request->get('email'));
            $fullname = $this->sanitizeInput($request->request->get('fullname'));
            $age = $this->sanitizeInput($request->request->get('age'));
            $phonenumber = $this->sanitizeInput($request->request->get('phonenumber'));

            $musician = $this->getUser();
    
            $entityManager = $this->getDoctrine()->getManager();
            $musician->setEmail($email);
            $musician->setPhone($phonenumber);
            $musician->setAge($age);
            $musician->setFullname($fullname);
            $entityManager->persist($musician);
            $entityManager->flush();
    
            $user = $this->getUser();

           
            return new JsonResponse($email);
        }

        // return $this->render('sell/index.html.twig');
    }

    /**
     * @Route("/save/skill", name="save_skill")
     */
    public function saveSkill(Request $request)
    {
        if($request->request->get('skill')){

            $skillname = $this->sanitizeInput($request->request->get('skill'));
            $level = $this->sanitizeInput($request->request->get('level'));
            $experience = $this->sanitizeInput($request->request->get('experience'));

            $musician = $this->getUser();
    
            $entityManager = $this->getDoctrine()->getManager();
            $skill = new Skill();
            $skill->setSkillname($skillname);
            $skill->setLevelofskill($level);
            $skill->setExperienceofskill($experience);
            $skill->setMusician($musician);
            $entityManager->persist($skill);
            $entityManager->flush();
           
            return new JsonResponse($skillname);
        }
        

    }

    /**
     * @Route("/save/education", name="save_education")
     */
    public function saveEducation(Request $request)
    {
        if($request->request->get('institution')){

            $from_year = $this->sanitizeInput($request->request->get('from_year'));
            $full_date_from = \DateTime::createFromFormat('Y-m', $from_year);
            
            $to_year = $this->sanitizeInput($request->request->get('to_year'));
            $full_date_to = \DateTime::createFromFormat('Y-m', $to_year); 

            $institution = $this->sanitizeInput($request->request->get('institution'));
            $level_of_course = $this->sanitizeInput($request->request->get('level_of_course'));
            $location = $this->sanitizeInput($request->request->get('location'));
            $course = $this->sanitizeInput($request->request->get('course'));

            $musician = $this->getUser();
    
            $entityManager = $this->getDoctrine()->getManager();
            $education = new Education();
            $education->setInstitution($institution);
            $education->setFromyear($full_date_from);
            $education->setToyear($full_date_to);
            $education->setCoursename($course);
            $education->setDegree($level_of_course);
            $education->setLocation($location);
            $education->setMusician($musician);
            $entityManager->persist($education);
            $entityManager->flush();
           
            return new JsonResponse($course);
        }
        

    }

    /**
     * @Route("/save/job", name="save_job")
     */
    public function saveJob(Request $request)
    {
        if($request->request->get('institution_job')){

            $from_year = $this->sanitizeInput($request->request->get('from_year_job'));
            $full_date_from = \DateTime::createFromFormat('Y-m', $from_year);
            
            $to_year = $this->sanitizeInput($request->request->get('to_year_job'));
            $full_date_to = \DateTime::createFromFormat('Y-m', $to_year); 

            $institution = $this->sanitizeInput($request->request->get('institution_job'));
            $current_or_not = $this->sanitizeInput($request->request->get('current_or_not'));
            $location = $this->sanitizeInput($request->request->get('location_job'));
            $job_title = $this->sanitizeInput($request->request->get('job_title'));

            $musician = $this->getUser();
    
            $entityManager = $this->getDoctrine()->getManager();

            $job = new Job();

            $job->setInstitution($institution);
            $job->setStartingfrom($full_date_from);
            $job->setEndedin($full_date_to);
            $job->setCurrentornot($current_or_not);
            $job->setLocation($location);
            $job->setJobtitle($job_title);
            $job->setMusician($musician);

            $entityManager->persist($job);
            $entityManager->flush();
           
            return new JsonResponse($institution);
        }
        

    }


    /**
     * @Route("/save/role", name="save_role")
     */
    public function saveRole(Request $request)
    {
        if($request->request->get('role')){

            $role = $this->sanitizeInput($request->request->get('role'));
            $medium = $this->sanitizeInput($request->request->get('medium'));

            $musician = $this->getUser();
    
            $entityManager = $this->getDoctrine()->getManager();
            $job = new JobToBeOffered();
            $job->setJobtitle($role);
            $job->setMedium($medium);
            $job->setMusician($musician);
            $entityManager->persist($job);
            $entityManager->flush();
           
            return new JsonResponse($medium. " " . $role);
        }
        

    }

    /**
     * @Route("/save/salary", name="save_salary")
     */
    public function saveSalary(Request $request)
    {
        // if($request->request->get('cur_salary')){

            $cur_salary = $this->sanitizeInput($request->request->get('cur_salary'));
            $exp_salary = $this->sanitizeInput($request->request->get('exp_salary'));
            $cur_salary == "" ? "nil" : $cur_salary;
            $exp_salary == "" ? "nil" : $exp_salary;

            $musician = $this->getUser();
    
            $entityManager = $this->getDoctrine()->getManager();
            $musician->setCurrentsalary($cur_salary);
            $musician->setExpectedsalary($exp_salary);

            $entityManager->persist($musician);
            $entityManager->flush();
           
            return new JsonResponse($exp_salary);
        // }
        

    }


    public function sanitizeInput($input){
        $cleanInput = trim(strip_tags($input));
        return $cleanInput;

    }


    /**
     * @Route("/reset_pwd", name="musician_reset_password")
     */
    public function reset_password(Request $request): Response
    {
        return $this->render('musician/reset_password.html.twig', []);
    }


}
