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
use App\Entity\Pro;
use App\Entity\Education;
use App\Entity\Job;
use App\Entity\JobToBeOffered;
use App\Entity\Project;
use App\Entity\Role;
use App\Entity\Specialty;
use App\Entity\Settings;
use App\Entity\Gallery;
use App\Entity\Document;
use App\Updates\ResetPwdManager;
use App\Updates\MembershipManager;
use App\Updates\ActivationManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\MusicianRepository;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
            $data = [];
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
     * @Route("/save/template", name="save_template")
     */
    public function saveTheme(Request $request)
    {
        if($request->request->get('theme_template')){

            $theme_template = $this->sanitizeInput($request->request->get('theme_template'));

            $musician = $this->getUser();
    
            $entityManager = $this->getDoctrine()->getManager();
            $musician->setPdfTheme($theme_template);
            $entityManager->persist($musician);
            $entityManager->flush();
           
            return new JsonResponse($theme_template);
        }
        

    }

    /**
     * @Route("/save/web/template", name="save_web_template")
     */
    public function saveWebTheme(Request $request)
    {
        if($request->request->get('theme_template')){

            $theme_template = $this->sanitizeInput($request->request->get('theme_template'));

            $musician = $this->getUser();
    
            $entityManager = $this->getDoctrine()->getManager();
            $musician->setWebTheme($theme_template);
            $entityManager->persist($musician);
            $entityManager->flush();
           
            return new JsonResponse($theme_template);
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
    public function saveSalary(ActivationManager $activationManager, Request $request)
    {
        // if($request->request->get('cur_salary')){

            $cur_salary = $this->sanitizeInput($request->request->get('cur_salary'));
            $exp_salary = $this->sanitizeInput($request->request->get('exp_salary'));
            $cur_salary == "" ? "nil" : $cur_salary;
            $exp_salary == "" ? "nil" : $exp_salary;

            $musician = $this->getUser();
            $email = $musician->getEmail();
            
            $username = $this->base64url_encode($musician->getUsername());
            if($musician->getConfirmed() == 'true'){
                //do nothing
            } else {
                if($activationManager->sendActivationEmail($email, $username)){
                    $this->addFlash('success', "Account created successfully! Please check your email for an account activation link. (Check spam folder if you can't find it) ");
                }
            }

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
                    $this->getParameter('projects_directory'),
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
            if($project->getProjectimage() != null ) {
                $current_photo_path = $this->getParameter('projects_directory')."/".$project->getProjectimage();
                unlink($current_photo_path);
            }
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

            $doc = $document->getDoc();

            return new JsonResponse("/download/".$doc);
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
                $setting->setMuske("false");
            }
            if($accounttype == 'muske'){
                $setting->setMuske("true");
                $setting->setPro("false");
            }
            if($accounttype == 'basic'){
                $setting->setMuske("false");
                $setting->setPro("false");
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

    /**
     * @Route("/musician/activate/{username}", name="musician_confirm_email")
     */
    public function confirmEmail(Request $request, $username): Response
    {
        $decoded_username = $this->base64url_decode($username);
        $musician = $this->getDoctrine()->getManager()->getRepository('App:Musician')->findByUsername($decoded_username)[0];
        if($musician){
            $entityManager = $this->getDoctrine()->getManager();
            $musician->setConfirmed("true");
            $entityManager->persist($musician);
            $entityManager->flush();            
            $message = "Your email has been successfully confirmed.";
            $success = true;
        } else {
            $message = "That username doesn't exist in our database. Whatever you're doing!.";
            $success = false;
        }
        return $this->render('musician/confirm_email.html.twig', [
            'success' => $success,
            'message' => $message,
            'username' => $decoded_username
        ]);
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
            $this->setTransparency($new_image, $old_image); 
            imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
            imagepng($new_image, "$updir" .  '/'. "$thumb_beforeword/" . "$img". ".png");
        }

        return $imgt;

    }

  
    function setTransparency($new_image, $image_source)
    {
       
            $transparencyIndex = imagecolortransparent($image_source);
            $transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);
            
            if ($transparencyIndex >= 0) {
                $transparencyColor    = imagecolorsforindex($image_source, $transparencyIndex);   
            }
           
            $transparencyIndex    = imagecolorallocate($new_image, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
            imagefill($new_image, 0, 0, $transparencyIndex);
            imagecolortransparent($new_image, $transparencyIndex);
       
    } 

    /**
     * @Route("/musician/password/reset", name="musician_reset_password", methods={"GET", "POST"})
     */
    public function resetPassword(ResetPwdManager $resetPwdManager, Request $request)
    {
        
        if($request->request->get('req')){

            $req = $this->sanitizeInput($request->request->get('req'));
            $email = $this->sanitizeInput($request->request->get('email'));
            $data = [];

            $musician = $this->getDoctrine()->getManager()->getRepository('App:Musician')->findByEmail($email);

            if($musician){
                $username = $this->base64url_encode($musician[0]->getUsername());
                if($resetPwdManager->sendResetEmail($email, $username)){
                    $this->addFlash('success', 'Notification mail was sent successfully');
                }
                $data['found'] = "Please check your email for a link to reset your password. (Check spam folder if you can't find it)";
            } else {
                $data['found'] = "We can not find that email in our system.";
            }
           

            return new JsonResponse($data['found']);

        }

        return $this->render('musician/reset_password.html.twig');

    }
    

    /**
     * @Route("/musician/password/new/{encoded_username}", name="musician_new_password", methods={"GET", "POST"})
     */
    public function newPassword(Request $request, UserPasswordEncoderInterface $encoder, MUsicianRepository $userRepository, $encoded_username)
    {

        if($request->request->get('username')){

            $username = $this->sanitizeInput($request->request->get('username'));
            $plainPassword = $this->sanitizeInput($request->request->get('password'));

            $user = $userRepository->findOneBy(['username' => $username]);
            if ($user === null) {
                $this->addFlash('danger', 'Invalid username');
                return new JsonResponse("can't find that username");
            }
            $password = $encoder->encodePassword($user, $plainPassword);

            $user->setPassword($password);
            $userRepository->flush();

            return new JsonResponse("homepage");
        } else {
            $decoded_username = $this->base64url_decode($encoded_username);
            return $this->render('musician/new_password.html.twig', [
                'decoded_username' => $decoded_username,
                'encoded_username' => $encoded_username,
            ]);
    
        }
        
    }


	function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
      
    function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
          

    /**
     * @Route("/musician/username/validate", name="musician_validate_username")
     */
    public function validateUsername(Request $request): Response
    {

        if($request->request->get('username')){

            $username = $this->sanitizeInput($request->request->get('username'));
            $musicians = $this->getDoctrine()->getManager()->getRepository('App:Musician')->findAll();
            $usernames = [];
            foreach ($musicians as $musician) {
                $usernames[] = $musician->getUsername();
            }

            if (preg_match('/[^A-Za-z0-9.#\\-$]/', $username)) {
                $message = "<p style='color:red'>Your username should not have spaces or special characters</p>";
            } else {

                if(in_array($username, $usernames)){
                    $message = "<p style='color:red'>That username is already taken</p>";
                } else {
                    $message = "<p style='color:green'>Username is available</p>";
                }

            }

            return new JsonResponse($message);

    
        }

    }

    /**
     * @Route("/change/membership", name="change_membership")
     */
    public function changeMembership(MembershipManager $membershipManager, Request $request)
    {
        if($request->request->get('membership')){

            $membership = $this->sanitizeInput($request->request->get('membership'));

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
            
            if($membership == 'pro'){
                $setting->setPro("true");
                $setting->setMuske("false");
                $started = new \DateTime("now");
                $started ->setTime(0, 0, 0);

                $ending = clone $started;
                $ending->modify('+365 days'); 
                $pro = new Pro();
                $pro->setMusician($musician);
                $pro->setStarted($started);
                $pro->setEnding($ending);
                $entityManager->persist($pro);
                $entityManager->flush();
            }
            if($membership == 'muske'){
                $setting->setMuske("true");
                $setting->setPro("false");
            }
            if($membership == 'basic'){
                $setting->setMuske("false");
                $setting->setPro("false");
            }

            $setting->setMusician($musician);
            $entityManager->persist($setting);
            $entityManager->flush();

            $email = $musician->getEmail();
            $data = [];

            if($membershipManager->sendMembershipConfirmation($email, $membership, $musician->getUsername())){
                $this->addFlash('success', 'Notification mail was sent successfully');
                $data['sent'] = "<p style='color:green'>A confirmation message has been sent to your email. (Check spam folder if you can't find it)</p>";
            } else {
                $data['sent'] = "<p style='color:red'>Your subscription was unsuccessful. We will call you soon</p>";
            }
            
            return new JsonResponse($data['sent']);
    
        }
        

    }

    /**
     * @Route("/download/{file}", name="download_pdf")
    **/
    public function downloadPdfAction($file){
        $folder = $this->getParameter('document_directory');
        $response = new BinaryFileResponse($folder."/".$file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,"$file.pdf");
        return $response;
    }    

}
