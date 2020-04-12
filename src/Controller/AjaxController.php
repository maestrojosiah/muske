<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Skill;


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
