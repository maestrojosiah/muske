<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
use App\Entity\Track;
use App\Entity\Document;
use App\Entity\Rating;
use App\Updates\ResetPwdManager;
use App\Updates\MessageFromResume;
use App\Updates\MembershipManager;
use App\Updates\ActivationManager;
use App\Updates\CallMeBack;
use App\Updates\SubmittedResume;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\MusicianRepository;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Repository\ProRepository;
use App\Repository\AdvertRepository;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use App\Service\OrganizeThings; 
use App\Service\SendSms; 
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use App\Repository\JobRepository;
use App\Repository\EducationRepository;

class AjaxController extends AbstractController
{
	private $pdf;
	private $twig;
    private $organizeThings;
    private $seoPage;
    private $jobRepo;
    private $educationRepo;

	public function __construct(JobRepository $jobRepository, EducationRepository $educationRepository, SeoPageInterface $seoPage, Pdf $pdf, Environment $twig, OrganizeThings $organizeThings)
	{
        $this->pdf = $pdf;		
        $this->twig = $twig;		
        $this->organizeThings = $organizeThings;		
        $this->seoPage = $seoPage;		
        $this->jobRepo = $jobRepository;
        $this->educationRepo = $educationRepository;
	}

    public function human_filesize($bytes, $decimals = 2) {
      $sz = 'BKMGTP';
      $factor = floor((strlen($bytes) - 1) / 3);
      return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }


    /**
     * @Route("/save/rating", name="save_rating")
     */
    public function saveRating(Request $request)
    {
        if($request->request->get('ratingval')){

            $ratingval = $this->sanitizeInput($request->request->get('ratingval'));
            $ratername = $this->sanitizeInput($request->request->get('ratername'));
            $rateremail = $this->sanitizeInput($request->request->get('rateremail'));
            $musician_id = $this->sanitizeInput($request->request->get('musician_id'));
            $musician = $this->getDoctrine()->getManager()->getRepository('App:Musician')->find($musician_id);
            $ratings = $this->getDoctrine()->getManager()->getRepository('App:Rating')->findAll();
            $entityManager = $this->getDoctrine()->getManager();

            if($ratings){
                foreach ($ratings as $rate ) {
                    if($rate->getRateremail() == $rateremail && $rate->getMusician() == $musician){
                        $rating = $rate;
                    } else {
                        $rating = new Rating();
                    }
                }
    
            } else {
                $rating = new Rating();
            }
            

            $rating->setMusician($musician);
            $rating->setRatingval($ratingval);
            $rating->setRater($ratername);
            $rating->setRateremail($rateremail);
            $entityManager->persist($rating);
            $entityManager->flush();
           
            return new JsonResponse($ratername);
        }
        

    }



    public function sanitizeInput($input){
        $cleanInput = trim(strip_tags($input));
        return $cleanInput;

    }


    /**
     * @Route("/musician/activate/{username}", name="musician_confirm_email")
     */
    public function confirmEmail(Request $request, MusicianRepository $musicianRepository, $username): Response
    {
        $this->seoPage->setTitle("Confirm email");

        $decoded_username = $this->base64url_decode($username);
        $musician = $musicianRepository->findOneByUsername($decoded_username);
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
    public function resetPassword(ResetPwdManager $resetPwdManager, MusicianRepository $musicianRepository, Request $request)
    {
        $this->seoPage->setTitle("Reset password");

        if($request->request->get('req')){

            $req = $this->sanitizeInput($request->request->get('req'));
            $email = $this->sanitizeInput($request->request->get('email'));
            $data = [];

            $musician = $musicianRepository->findByEmail($email);

            if($musician){
                $username = $this->base64url_encode($musician[0]->getUsername());
                if($resetPwdManager->sendResetEmail($email, $username)){
                    $this->addFlash('success', 'Notification mail was sent successfully');
                    $data['found'] = "Please check your email for a link to reset your password. (Check spam folder if you can't find it)";
                }
                
            } else {
                $data['found'] = "We can not find that email in our system.";
            }
           

            return new JsonResponse($data['found']);

        }

        return $this->render('musician/reset_password.html.twig');

    }
    
    /**
     * @Route("/musician/receive/message", name="musician_receive_message", methods={"GET", "POST"})
     */
    public function sendResumeMessage(MessageFromResume $messageFromResume, Request $request, SendSms $sendSms)
    {
        
        if($request->request->get('senderemail')){

            $senderemail = $this->sanitizeInput($request->request->get('senderemail'));
            $sendername = $this->sanitizeInput($request->request->get('sendername'));
            $senderphone = $this->sanitizeInput($request->request->get('senderphone'));
            $message = $this->sanitizeInput($request->request->get('message'));
            $calltime = $this->sanitizeInput($request->request->get('calltime'));
            $musician_id = $this->sanitizeInput($request->request->get('musician_id'));
            $data = [];

            $musician = $this->getDoctrine()->getManager()->getRepository('App:Musician')->find($musician_id);

            if($musician){
                if($messageFromResume->sendEmailMessage($musician->getEmail(), $musician->getUsername(), $sendername, $senderemail, $message, $senderphone, $calltime)){
                    $to = $musician->getFormattedNumber();
                    $msg = 'Resume message. Check your email if you\'re a pro member. Not pro? MuSKe will contact you';
            
                    if ($to !== "" && $msg !== "") {
            
                        $data1 = "{\n \"from\":\"ModuleZilla\",\n \"to\":\"$to\",\n \"text\":\"$msg\"\n}";
            
                        try {
                            $response = $sendSms->CallAPI('POST', 'http://54.247.191.102/restapi/sms/1/text/single', $data1 );
                            $data['res'] = $response;
                        } catch (HttpException $ex) {
                            $data['res'] = $ex;
                        }
                        
                    }
                    $data['found'] = "Message has been sent";
                }
                
            } else {
                $data['found'] = "We can not find that email in our system.";
            }
           

            return new JsonResponse($data);

        }

    }
    
    /**
     * @Route("/musician/receive/callback", name="call_me_back")
     */    public function sendCallBack(CallMeBack $callMeBack, Request $request)
    {
        
        if($request->request->get('client_phone_number')){

            $client_phone_number = $this->sanitizeInput($request->request->get('client_phone_number'));
            $musician_id = $this->sanitizeInput($request->request->get('musician_id'));

            $musician = $this->getDoctrine()->getManager()->getRepository('App:Musician')->find($musician_id);

            if($musician){
                if($callMeBack->sendEmailMessage($musician->getEmail(), $musician->getFullname(), $client_phone_number, $musician->getUsername())){
                    // $this->addFlash('success', 'Notification mail was sent successfully');
                    $message = "Email has ben sent successfully";
                }
                
            } 
           

            return new JsonResponse($message);

        }

    }
    


    /**
     * @Route("/musician/password/new/{encoded_username}", name="musician_new_password", methods={"GET", "POST"})
     */
    public function newPassword(Request $request, UserPasswordEncoderInterface $encoder, MusicianRepository $userRepository, $encoded_username)
    {
        $this->seoPage->setTitle("Change password");

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
                $result = 'error';
            } else {

                if(in_array($username, $usernames)){
                    $message = "<p style='color:red'>That username is already taken</p>";
                    $result = 'error';
                } else {
                    $message = "<p style='color:green'>Username is available</p>";
                    $result = 'success';
                }

            }

            return new JsonResponse([$result, $message]);

    
        }

    }

    /**
     * @Route("/change/membership", name="change_membership")
     */
    public function changeMembership(MembershipManager $membershipManager, ProRepository $proRepository, Request $request)
    {
        if($request->request->get('membership')){

            $membership = $this->sanitizeInput($request->request->get('membership'));
            $entityManager = $this->getDoctrine()->getManager();

            // update musician
            $musician = $this->getUser();
            $musician->setAccount($membership);
            $entityManager->persist($musician);
            $entityManager->flush();
    
            
            if($membership == 'pro'){

                $started = new \DateTime("now");
                $started ->setTime(0, 0, 0);

                $ending = clone $started;
                $ending->modify('+365 days'); 

                $prevPro = $proRepository->findOneBy(
                    array('musician' => $musician)
                );
                
                if($prevPro) {
                    $pro = $prevPro;
                } else {
                    $pro = new Pro();
                }

                $pro->setMusician($musician);
                $pro->setStarted($started);
                $pro->setEnding($ending);
                $entityManager->persist($pro);
                $entityManager->flush();
            }

            $email = $musician->getRealEmail();
            $data = [];

            if($membershipManager->sendMembershipConfirmation($email, $membership, $musician->getUsername())){
                $this->addFlash('success', 'Notification mail was sent successfully');
                $data['sent'] = "Your subscription was successful! A confirmation message has been sent to your email. (Check spam folder if you can't find it). ";
            } else {
                $data['sent'] = "<p style='color:red'>Your subscription was successful. We will call you soon</p>";
            }
            
            return new JsonResponse($data['sent']);
    
        }
        

    }

    /**
     * @Route("/track/advert", name="advert_track")
     */
    public function track(Request $request, AdvertRepository $advertRepository): Response
    {
        $phone = $request->request->get('phone');
        $code = $request->request->get('code');

        $url = $this->generateUrl('advert_tracking', [
            'phone' => $phone,
            'code' => $code,
        ]);

        return new JsonResponse($url);

    }
            
    /**
     * @Route("/mark/notification/read", name="mark_notification_read")
     */
    public function markNotificationRead(Request $request){
        $id = $request->request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $notification = $entityManager->getRepository('App:Notification')->find($id);
        $track = new Track();
        $track->setMusician($this->getUser());
        $track->setAdvert($notification->getAdvert());
        $track->setType('opened');
        $track->setUpdated(new \DateTime("now"));
        $entityManager->persist($track);
        $entityManager->flush();
        $entityManager->remove($notification);
        $entityManager->flush();
        $url = $this->generateUrl('advert_index', [
            'id' => $track->getAdvert()->getId()
        ]);
        return new JsonResponse($url);
    }

    /**
     * @Route("/ad/track/page", name="ad_tracking")
     */
    public function adTrackingFromAdPage(Request $request, AdvertRepository $advertRepository, SubmittedResume $submittedResume, Pdf $pdf){
        $entityManager = $this->getDoctrine()->getManager();
        $ad_id = $request->request->get('ad_id');
        $activity = $request->request->get('activity');
        $advert = $advertRepository->find($ad_id);
        $musician = $this->getUser();
        $data = [];


        $notification = $entityManager->getRepository('App:Notification')->findOneBy(
            ['musician' => $this->getUser(), 'advert' => $advert],
            ['id' => 'DESC']
        );

        if(null !== $notification){
            $entityManager->remove($notification);
            $entityManager->flush();    
        }

        $duplicateTrack = $entityManager->getRepository('App:Track')->findOneBy(
            ['musician' => $this->getUser(), 'advert' => $advert, 'type' => $activity],
            ['id' => 'ASC']
        );
        if($duplicateTrack){
            $track = $duplicateTrack;
        } else {
            $track = new Track();
        }

        if($activity == 'sent'){

            $jobs = $this->organizeThings->organizedJobsAccordingToSettings($musician);
            $educ = $this->organizeThings->organizedEducationAccordingToSettings($musician);
            
    
            $photourl = str_replace('/home/maestrojosiah/projects/muske/public', '', $this->getParameter('brochures_directory')."/thumbs/".$musician->getPhoto().".png");
            $placeholder = str_replace('/home/maestrojosiah/projects/muske/public', '', $this->getParameter('img_directory')."/headshot.jpg");
            $pdf_template = $musician->getPdfTheme() ? $musician->getPdfTheme() : 'simpleOne.html.twig' ;

            $thumbnailurl = strlen($musician->getPhoto()) > 1 ?  $photourl : $placeholder;
                
            $html = $this->twig->render("pdf/$pdf_template", [
                'musician' => $this->getUser(),
                'jobs' => $jobs,
                'educ' => $educ,
            ]);
            $mypdf = $this->pdf->getOutputFromHtml($html);
                        
            $attachment = new \Swift_Attachment($mypdf, $musician->getFullname()."_resume.pdf", 'application/pdf');
    
            if($submittedResume->sendEmailMessage($advert->getEmail(), $advert->getInstitution(), $this->getUser()->getUsername(), $attachment)){
                $data['found'] = "Message has been sent";
            }

        }
        
        $track->setMusician($this->getUser());
        $track->setAdvert($advert);
        $track->setType($activity);
        $track->setUpdated(new \DateTime("now"));
        $entityManager->persist($track);
        $entityManager->flush();
        $url = $this->generateUrl('advert_index', [
            'id' => $advert->getId()
        ]);
        return new JsonResponse($url);
    }

    /**
     * @Route("/suggest/matching/musicians", name="suggest_musicians")
     */
    public function suggestAction(Request $request)
    {
        $search_text = $request->request->get('search_text');
    
        // $matchingSkills = $skillRepository->searchFromSkills($search_text);
        // $matchingRoles = $roleRepository->searchFromRoles($search_text);

        $search_text = [];

        foreach ($books as $key => $book) {
            $search_text[] = [
                $book->getTitle(), 
                $book->getAuthor(), 
                $book->getCategory(), 
                substr($book->getDescription(), 0, 100)."...",
                $this->generateUrl("book_show", ['id' => $book->getId()])
             ];
        }
        return new JsonResponse($search_text);
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

    /**
     * @Route("/save-entity-cv", name="save_entity_cv")
     */
    public function save( $myClass = "", $array_data = [], Request $request )
    {
        if(null !== $request->request->get('class') || $myClass != null){
            
            // class from form
            $classFromForm = $myClass == "" ? $request->request->get('class') : $myClass ;

            // declarations
            $entityManager = $this->getDoctrine()->getManager();
            
            //check if classname contains a hyphen and id, use existing entity, else, new
            if(strpos($classFromForm, '-')){
                $class_name = explode('-', $classFromForm)[0]; // get the class name
                $id = explode('-', $classFromForm)[1]; // get the second part of the string - id
                $class = "App:".$class_name; // make a string of the entity
                $entity = $entityManager->getRepository($class)->find($id); // get the entity by id
            } else { // if new entity
                $class = "App\Entity\\".$classFromForm; // make a string of the entity
                $entity = new $class(); // instantiate
            }            

            // for checking entity attributes
            $docReader = new AnnotationReader();
            $reflect = new \ReflectionClass($entity);
                   
            // for testing. delete when done
            $data = [];
            $my_data_array = empty($array_data) ? $request->request->all() : $array_data;

            // go through all the request data attributes
            foreach ($my_data_array as $key => $item ) {
                // var_dump($key);

                // see if there is a property by the name $key
                if (!$reflect->hasProperty($key)) {
                    // var_dump('the entity does not have such a property called '.$key);
                } else {
                    
                    // if the property name exists, assign it to propInfos variable
                    $propInfos = $docReader->getPropertyAnnotations($reflect->getProperty($key));

                    // capitalize the first letter of the $key so as to concatenate it to "set".
                    $key = $this->dashesToCamelCase($key);
                    $key = ucfirst($this->sanitizeInput($key));

                    // sanitize the item to be saved
                    $item = $this->sanitizeInput($item);

                    // create a string method eg. setFirstname.
                    $setter = "set".$key;
                    
                    // check if the property to be saved is a date
                    if ( $propInfos[0]->type === 'date' ) {

                        // if it is a date then do the necessary
                        $date = new \DateTime("$item");

                        // set it using the setter created above setFirstname("John")
                        $entity->$setter($date);

                    } else {

                        // if it is not a date, just set it : setFirstname("John")
                        $entity->$setter($item);

                    } 
                }
                
                $data[$key] = $item;
                
            }

            // in case there were extras..
            $extras = isset($data['extras']) ? $data['extras'] : null;
            if($extras != null) {
                if(strpos($extras, '-')){ // if more than one extra
                    // explode them using '-'
                    $all_extras = explode('-',$extras);
                } else {
                    $all_extras = [$extras];
                }
                

                // iterate through the extras array 
                foreach ($all_extras as $extra ) {

                    if(strpos($extra, '#')){
                        $arrSplit = explode('#', $extra);
                        $extra = $arrSplit[0];
                        $id = $arrSplit[1]; 
                    } else { 
                        $id = "";
                    }
        
                    // the provide_ function that are manually created for special cases
                    $function = "provide_$extra";
                    $key = ucfirst($this->sanitizeInput($extra));
                    $item = $this->{$function}($id);

                    // set the values
                    $setter = "set".$key;
                    $data[$key] = $item;
                    $entity->$setter($item);

                }
            }

            // persist and flush
            $entityManager->persist($entity);
            $entityManager->flush();

            if(null != $request->request->get('return')) {
                $toReturn = str_replace($request->request->get('texttoreplace'),$entity->getId(),$request->request->get('return'));
                if(strtolower($classFromForm) == "job" && $entity){
                    $musician = $this->getUser();

                    $em = $this->getDoctrine()->getManager();
                    $repoJobs = $em->getRepository(Job::class)->findBy(
                        ['musician' => $musician],
                        ['id' => 'ASC']
                    );
                    $totalJobs = count($repoJobs);
                    if($totalJobs == 1){
                        $toReturn .= '<div class="alert alert-info alert-dismissible fade show" role="alert">
                        <strong>Add roles: </strong> You can add roles here by typing the roles you played in each job. <i class="fa fa-hand-o-up" aria-hidden="true"></i>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                       </div>';
                    }
                }
            } else {
                $toReturn = "";
            }
            
            // return html
            return new JsonResponse($toReturn);

        } 

    }

    public function provide_musician($id){
        $musician = $this->getUser();
        return $musician;
    }

    public function provide_job($id){
        $job = $this->jobRepo->find($id);
        return $job;
    }

    public function provide_education($id){
        $edu = $this->educationRepo->find($id);
        return $edu;
    }

    function dashesToCamelCase($string, $capitalizeFirstCharacter = false) 
    {
    
        $str = str_replace('_', '', ucwords($string, '_'));
    
        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }
    
        return $str;
    }



}
