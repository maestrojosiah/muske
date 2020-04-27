<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Skill;
use App\Entity\Education;
use App\Entity\Job;
use App\Entity\JobToBeOffered;
use App\Entity\Project;
use App\Entity\Role;
use App\Entity\Specialty;
use App\Entity\Settings;
use App\Entity\Gallery;
use App\Entity\Document;


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

            $musician = $this->getUser();
    
            $entityManager = $this->getDoctrine()->getManager();
            $job = new JobToBeOffered();
            $job->setJobtitle($role);
            $job->setMusician($musician);
            $entityManager->persist($job);
            $entityManager->flush();
           
            return new JsonResponse($role);
        }
        

    }



    /**
     * @Route("/save/job/role", name="save_job_role")
     */
    public function saveJobRole(Request $request)
    {
        if($request->request->get('job_role')){

            $job_role = $this->sanitizeInput($request->request->get('job_role'));
            $job_id = $this->sanitizeInput($request->request->get('job_id'));

            $job = $this->getDoctrine()->getManager()->getRepository('App:Job')->find($job_id);
    
            $entityManager = $this->getDoctrine()->getManager();
            $role = new Role();
            $role->setRole($job_role);
            $role->setJob($job);
            $entityManager->persist($role);
            $entityManager->flush();
           
            return new JsonResponse($job_role);
        }
        

    }

    /**
     * @Route("/save/edu/skill", name="save_edu_skill")
     */
    public function saveEduSkill(Request $request)
    {
        if($request->request->get('skill')){

            $edu_skill = $this->sanitizeInput($request->request->get('skill'));
            $education_id = $this->sanitizeInput($request->request->get('education_id'));

            $education = $this->getDoctrine()->getManager()->getRepository('App:Education')->find($education_id);
    
            $entityManager = $this->getDoctrine()->getManager();
            $skill = new Specialty();
            $skill->setInstrumentorskill($edu_skill);
            $skill->setEducation($education);
            $entityManager->persist($skill);
            $entityManager->flush();
           
            return new JsonResponse($edu_skill);
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

    /**
     * @Route("/save/project", name="save_project")
     */
    public function saveProject(Request $request)
    {
        // if($request->request->get('project_title')){

            $project_title = $this->sanitizeInput($request->request->get('project_title'));
            $project_description = $this->sanitizeInput($request->request->get('project_description'));
            $project_title == "" ? "nil" : $project_title;
            $project_description == "" ? "nil" : $project_description;

            $musician = $this->getUser();
            $project = new Project();
    
            $entityManager = $this->getDoctrine()->getManager();
            $project->setProjecttitle($project_title);
            $project->setProjectdescription($project_description);
            $project->setMusician($musician);

            $entityManager->persist($project);
            $entityManager->flush();
           
            return new JsonResponse($project_title);
        // }
        

    }


    /**
     * @Route("/upload/project/image", name="upload_project_image")
     */
    public function uploadProjImage(Request $request)
    {

        $project_id = $this->sanitizeInput($request->request->get('id'));
        $file = $request->files->get('doc');
        $user = $this->getUser();
        
        if($file){
            $filename = md5(uniqid()).'.'.$file->guessExtension();
            // Move the file to the directory where brochures are stored
            try {
                $file->move(
                    $this->getParameter('brochures_directory'),
                    $filename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            return new JsonResponse($filename);        

        }
        
        if($request->request->get('filename')){

            $filename = $this->sanitizeInput($request->request->get('filename'));
            $project = $this->getDoctrine()->getManager()->getRepository('App:Project')->find($project_id);
            $project->setProjectimage($filename);
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            $img_link = $project->imagelink();

            return new JsonResponse($img_link);
        }
        return new JsonResponse("fail");
    }


    /**
     * @Route("/upload/profile/picture", name="change_profile_picture")
     */
    public function uploadProfPic(Request $request)
    {

        $file = $request->files->get('doc');
        $musician = $this->getUser();
        
        if($file){
            if($musician->getPhoto() != null ) {
                $current_photo_path = $this->getParameter('brochures_directory')."/".$musician->getPhoto();
                $current_photo_thumb_path = $this->getParameter('brochures_directory')."/thumbs/".$musician->getPhoto();
                if(file_exists($current_photo_path)){ unlink($current_photo_path); }
                if(file_exists($current_photo_thumb_path)){ unlink($current_photo_thumb_path); }
            }

            $filename = md5(uniqid()).'.'.$file->guessExtension();
            // Move the file to the directory where brochures are stored
            try {
                $file->move(
                    $this->getParameter('brochures_directory'),
                    $filename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            return new JsonResponse($filename);        

        }
        
        if($request->request->get('filename')){

            $filename = $this->sanitizeInput($request->request->get('filename'));
            $musician->setPhoto($filename);
            $em = $this->getDoctrine()->getManager();
            $em->persist($musician);
            $em->flush();

            $updir = $this->getParameter('brochures_directory');
            $img = $musician->getPhoto();
            $this->makeThumbnails($updir, $img, 300, 300);


            $img_link = $musician->photourl();

            return new JsonResponse($img_link);
        }
        return new JsonResponse("fail");
    }

    /**
     * @Route("/upload/education/document", name="upload_education_document")
     */
    public function uploadEduDoc(Request $request)
    {

        $edu_id = $this->sanitizeInput($request->request->get('id'));
        $file = $request->files->get('doc');
        $musician = $this->getUser();
        
        if($file){
            $filename = md5(uniqid()).'.'.$file->guessExtension();
            // Move the file to the directory where brochures are stored
            try {
                $file->move(
                    $this->getParameter('document_directory'),
                    $filename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            return new JsonResponse($filename);        

        }
        
        if($request->request->get('filename')){

            $filename = $this->sanitizeInput($request->request->get('filename'));
            $education = $this->getDoctrine()->getManager()->getRepository('App:Education')->find($edu_id);
            $edu_doc = $education->getDocument();
            if($edu_doc){
                $document = $edu_doc;
            } else {
                $document = new Document();
            }
        
            $document->setEducation($education);
            $document->setMusician($musician);
            $document->setDoc($filename);
            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();

            $doc_link = $document->getDocpath();

            return new JsonResponse($doc_link);
        }
        return new JsonResponse("fail");
    }

    /**
     * @Route("/upload/gallery/image", name="upload_gallery_image")
     */
    public function uploadGalleryImage(Request $request)
    {

        $file = $request->files->get('doc');
        $musician = $this->getUser();
        
        if($file){
            $filename = md5(uniqid()).'.'.$file->guessExtension();
            // Move the file to the directory where brochures are stored
            try {
                $file->move(
                    $this->getParameter('gallery_directory'),
                    $filename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            return new JsonResponse($filename);        

        }
        
        if($request->request->get('filename')){

            $filename = $this->sanitizeInput($request->request->get('filename'));
            $description = $this->sanitizeInput($request->request->get('description'));
            $type = "photo";
            $image = new Gallery();
            $image->setPhoto($filename);
            $image->setDescription($description);
            $image->setType($type);
            $image->setMusician($musician);
            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();

            $updir = $this->getParameter('gallery_directory');
            $img = $image->getPhoto();
            $this->makeThumbnails($updir, $img);

            $img_link = $image->getPhoto();

            return new JsonResponse($img_link);
        }
        return new JsonResponse("fail");
    }

    /**
     * @Route("/save/setting", name="save_settings")
     */
    public function saveSetting(Request $request)
    {
        if($request->request->get('order_job')){

            $order_job = $this->sanitizeInput($request->request->get('order_job'));
            $order_job_by = $this->sanitizeInput($request->request->get('order_job_by'));
            $order_education = $this->sanitizeInput($request->request->get('order_education'));
            $order_education_by = $this->sanitizeInput($request->request->get('order_education_by'));
            $facebook = $this->sanitizeInput($request->request->get('facebook'));
            $twitter = $this->sanitizeInput($request->request->get('twitter'));
            $linkedin = $this->sanitizeInput($request->request->get('linkedin'));
            $youtube = $this->sanitizeInput($request->request->get('youtube'));
            $instagram = $this->sanitizeInput($request->request->get('instagram'));
            $online = $this->sanitizeInput($request->request->get('online'));
            $tsc = $this->sanitizeInput($request->request->get('tsc'));
            $wheretowork = $this->sanitizeInput($request->request->get('wheretowork'));
            $accounttype = $this->sanitizeInput($request->request->get('accounttype'));

            $musician = $this->getUser();
    
            $entityManager = $this->getDoctrine()->getManager();

            $settings = $entityManager
			->getRepository('App:Settings')
			->findBy(
				array('musician' => $musician)
            );
            
            if($settings){
                $setting = $settings[0];
            } else {
                $setting = new Settings();
            }
            
            if($accounttype == 'pro'){
                $setting->setPro("true");
            }
            if($accounttype == 'muske'){
                $setting->setMuske("true");
            }

            $setting->setJobOrder($order_job);
            $setting->setJobOrderBy($order_job_by);
            $setting->setEduOrder($order_education);
            $setting->setEduOrderBy($order_education_by);
            $setting->setFacebook($facebook);
            $setting->setTwitter($twitter);
            $setting->setLinkedin($linkedin);
            $setting->setYoutube($youtube);
            $setting->setInstagram($instagram);
            $setting->setOnline($online);
            $setting->setTsc($tsc);
            $setting->setPlaceofwork($wheretowork);
            $setting->setMusician($musician);
            $entityManager->persist($setting);
            $entityManager->flush();
           
            return new JsonResponse($order_education);
        }
        

    }

    /**
     * @Route("/upload/link", name="upload_link")
     */
    public function uploadLink(Request $request)
    {
        if($request->request->get('url')){

            $url = $this->sanitizeInput($request->request->get('url'));
            $description = $this->sanitizeInput($request->request->get('description_link'));

            $musician = $this->getUser();
            $entityManager = $this->getDoctrine()->getManager();

            $link = new Gallery();
            $link->setPhoto($url);
            $link->setDescription($description);
            $link->setType("video");
            $link->setMusician($musician);
            $entityManager->persist($link);
            $entityManager->flush();
           
            return new JsonResponse($link);
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

    
    public function makeThumbnails($updir, $img, $thumbnail_width="300", $thumbnail_height="200")
    {

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
