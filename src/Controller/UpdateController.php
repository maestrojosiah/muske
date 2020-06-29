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
use App\Entity\Document;
use App\Entity\Rating;
use App\Entity\Advert;
use App\Entity\Musician;
use App\Entity\Notification;
use App\Entity\Track;
use App\Updates\ResetPwdManager;
use App\Updates\MessageFromResume;
use App\Updates\MembershipManager;
use App\Updates\ActivationManager;
use App\Updates\CallMeBack;
use App\Updates\AdTrackingCode;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\MusicianRepository;
use App\Repository\DocumentRepository;
use App\Repository\SettingsRepository;
use App\Repository\NotificationRepository;
use App\Repository\JobRepository;
use App\Repository\EducationRepository;
use App\Repository\AdvertRepository;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Repository\ProRepository;
use Doctrine\Common\Annotations\AnnotationReader;

class UpdateController extends AbstractController
{
    private $jobRepo;
    private $documentRepo;
    private $musicianRepo;
    private $settingsRepo;
    private $educationRepo;
    private $notificationRepo;
    private $advertRepo;
    private $postRepo;
    private $adTrackingCode;
    private $activationManager;
    
    public function __construct(ActivationManager $activationManager, PostRepository $postRepository, AdvertRepository $advertRepository, AdTrackingCode $adTrackingCode, NotificationRepository $notificationRepository, EducationRepository $educationRepository, SettingsRepository $settingsRepository, MusicianRepository $musicianRepository, JobRepository $jobRepository, DocumentRepository $documentRepository)
    {
        $this->jobRepo = $jobRepository;
        $this->documentRepo = $documentRepository;
        $this->musicianRepo = $musicianRepository;
        $this->settingsRepo = $settingsRepository;
        $this->educationRepo = $educationRepository;
        $this->notificationRepo = $notificationRepository;
        $this->advertRepo = $advertRepository;
        $this->postRepo = $postRepository;
        $this->adTrackingCode = $adTrackingCode;
        $this->activationManager = $activationManager;

    }

    /**
     * @Route("/save-entity", name="save_entity")
     */
    public function save( $myClass = "", $array_data = [], Request $request, $toReturn = 'nothingToReturn' )
    {
        if(null !== $request->request->get('class') || $myClass != null){
            
            // class from form
            $classFromForm = $myClass == "" ? $request->request->get('class') : $myClass ;
            if(null != $request->request->get('return')) {
                $toReturn = ucfirst($request->request->get('return'));
                $getter = "get".$toReturn;
            } else if ($toReturn != "nothingToReturn"){
                $getter = "get".$toReturn;
            }
            if ($classFromForm == 'Settings') { // special case of where browser needed to be refreshed to update settings hyphen and remove musician extra
                $settingsforThisMusician = $this->settingsRepo->findOneByMusician($this->getUser());
                if(null !== $settingsforThisMusician ){
                    $classFromForm = "Settings-".$settingsforThisMusician->getId();
                }
            }
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

                // for testing purposes. remove when done
                if ($class == 'App:Settings') {
                    $settingsforThisMusician = $this->settingsRepo->findOneByMusician($this->getUser());
                    if(null !== $settingsforThisMusician ){
                        $item = str_replace('musician', '', $item);
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
        //    var_dump($entity->getId());
            if(null !== $request->request->get('notification')){
                $type = $request->request->get('notification');
                if($type == 'advert'){
                    if($this->adTrackingCode->sendEmailMessage($entity->getEmail(), $entity->getInstitution(), $entity->getCode())){
                        // $this->addFlash('success', 'Notification mail was sent successfully');
                    }
                }
                $id = $entity->getId();
                $this->provide_notification($id, $type);
            }
            if(null !== $request->request->get('fullname')){
                $email = $request->request->get('email');
                $username = $this->base64url_encode($entity->getUsername());
                if($entity->getConfirmed() == 'true'){
                    //do nothing
                } else {
                    if($this->activationManager->sendActivationEmail($email, $username)){
                        $this->addFlash('success', "Account created successfully! Please check your email for an account activation link. (Check spam folder if you can't find it) ");
                    }    
                }
    
            }
            // value to return if needed
            $returnVal = $toReturn == 'nothingToReturn' ? 'nothingToReturn' : $entity->$getter();
            // var_dump($returnVal);

            // return testing data
            return new JsonResponse($returnVal);

        } 

    }

    /**
     * @Route("/upload/file/any", name="save_file")
     */
    public function saveFile(Request $request)
    {
        $file = $request->files->get('file');
        $path_to_save = $request->request->get('path');
        $simplepath = $request->request->get('simplepath');
        $myClass = $request->request->get('myClass');
        $field = $request->request->get('field');
        $shape = $request->request->get('shape');
        $description = $request->request->get('description');
        $extras = $request->request->get('extras') == "" ? null : $request->request->get('extras');
        $getString = "get" . ucfirst($field);

        if(strpos($myClass, '-')){
            $class = explode('-', $myClass)[0]; // get the second part of the string - id
            $id = explode('-', $myClass)[1]; // get the second part of the string - id
            $funcString = strtolower($class)."Repo";
            $repo = $this->$funcString->find($id);
            $delete = false;
        } 
        
        if($file){

            if( isset($repo) ){
                $current_photo_path = $this->getParameter($path_to_save)."/".$repo->$getString();
                $current_photo_thumb_path = $this->getParameter($path_to_save)."/thumbs/".$repo->$getString().".png";
                if($repo->$getString() != "drumset-half.jpg" and $repo->$getString() != "class.jpg"){
                    if(is_file($current_photo_path) && file_exists($current_photo_path)){ unlink($current_photo_path); }
                    if(is_file($current_photo_thumb_path) && file_exists($current_photo_thumb_path)){ unlink($current_photo_thumb_path); }        
                }
            }

            $filename = md5(uniqid()).'.'.$file->guessExtension();
            // Move the file to the directory where brochures are stored
            try {
                $file->move(
                    $this->getParameter($path_to_save),
                    $filename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // $half_path = str_replace("/home/maestrojosiah/projects/muske/public", "", $this->getParameter($path_to_save));
            $half_path = $simplepath;
            
            if($field != 'doc'){
                $updir = $this->getParameter($path_to_save);
                $img = $filename;
                $this->makeThumbnails($updir, $img, 300, 300, $shape); 
                $link = $half_path."/thumbs/".$filename;   
            } else {
                $link = $half_path."/".$filename;
            }
            
            $this->save( 
                $myClass, 
                $array_data = [
                    $field => $filename,
                    'description' => $description,
                    'return' => $field,
                    'extras' => $extras
                ], 
                $request,
                $field
             );

            // var_dump($description);
             
            return new JsonResponse($link);
            
        }
        // return new JsonResponse("fail");
    }

    public function sanitizeInput($input){
        $cleanInput = trim(strip_tags($input));
        return $cleanInput;

    }

    public function provide_code($id){
        $random = random_int(1000, 10000);
        return $random;
    }

    public function provide_submitted($id){
        $started = new \DateTime("now");
        return $started;
    }

    public function provide_updated($id){
        $started = new \DateTime("now");
        return $started;
    }

    public function provide_notification($id, $type){
        $entityManager = $this->getDoctrine()->getManager();
        $musicians = $this->musicianRepo->findAll();
        if($type == "advert") {
            $array = ['entity' => 'Advert', 'field' => 'task'];
        } else if ($type == "postlike"){
            $array = ['entity' => 'Post', 'field' => 'title'];
        }
        
        foreach ($musicians as $key => $musician) {
            // $duplicate = $this->notificationRepo->findOneBy(
            //     ['musician' => $musician, 'type' => $type],
            //     ['id' => 'ASC']
            // );
            // if($duplicate){
            //     $notification = $duplicate;
            // } else {
            //     $notification = new Notification();
            // }
            $notification = new Notification();
            if($type == 'advert') {
                $notification->setAdvert($this->advertRepo->find($id));
            } else {
                $notification->setPost($this->postRepo->find($id));
            }
            $notification->setMusician($musician);
            $notification->setSender("Advert Manager");
            $notification->setUnread(1);
            $notification->setType("advert");
            $notification->setParameters($array);
            $notification->setReference($id);
            $notification->setCreated($this->provide_submitted($id));
            $entityManager->persist($notification);
            $entityManager->flush();
    
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

    public function provide_advert($id){
        $advert = $this->advertRepo->find($id);
        var_dump($advert);
        die();
        // return $advert;
    }

    function dashesToCamelCase($string, $capitalizeFirstCharacter = false) 
    {
    
        $str = str_replace('_', '', ucwords($string, '_'));
    
        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }
    
        return $str;
    }


    public function makeThumbnails($updir, $img, $thumbnail_width="300", $thumbnail_height="200", $shape="")
    {

        $thumb_beforeword = "thumbs";

        $arr_image_details = getimagesize("$updir" .  '/'. "$img"); 

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

        list($imgcreatefrom, $imgt) = $this->imageCreateFrom($arr_image_details[2]);

        if ($imgt) {
            $old_image = $imgcreatefrom("$updir" .  '/'. "$img");
            if($shape == 'square'){
                $new_image = $this->toSquare($old_image);
            } else if ($shape == 'rectangle') {
                $new_image = $this->toRectangle($old_image);
            } else if ($shape == 'gallery') {
                // forGallery($imgcreatefrom);
                $new_image = $this->resizeImage("$updir" .  '/'. "$img", 255, 170);
            } else {
                $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
                $this->setTransparency($new_image, $old_image); 
                imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
            }
            imagepng($new_image, "$updir" .  '/'. "$thumb_beforeword/" . "$img". ".png");
        }

        return $imgt;

    }

    public function imageCreateFrom($detail){

        if ($detail == IMAGETYPE_GIF) {
            $imgt = "ImageGIF";
            $imgcreatefrom = "ImageCreateFromGIF";
        }
        if ($detail == IMAGETYPE_JPEG) {
            $imgt = "ImageJPEG";
            $imgcreatefrom = "ImageCreateFromJPEG";
        }
        if ($detail == IMAGETYPE_PNG) {
            $imgt = "ImagePNG";
            $imgcreatefrom = "ImageCreateFromPNG";
        }

        return [$imgcreatefrom, $imgt];

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
    

    function toSquare($im){
        // $im = imagecreatefrompng($image);
        $size = min(imagesx($im), imagesy($im));
        $im2 = imagecrop($im, ['x' => 0, 'y' => 0, 'width' => $size, 'height' => $size]);
        // if ($im2 !== FALSE) {
        //     imagepng($im2, 'example-cropped.png');
        //     imagedestroy($im2);
        // }
        // imagedestroy($im);
        return $im2;

    }

    // for cover photo
    function toRectangle($im){
        $width = imagesx($im);
        $height = imagesy($im);
        if($height >= $width){
            $new_height = $width/4;
        } else if ($height < $width && $height > 800 ){
            $new_height = 500;
        } else if ($height >= 600){
            $new_height = 500;
        } else if ($height <= 500 && $height < $width){
            $new_height = $height;
        } else if ($height < $width && $height > 500 && $height <= 800){
            $new_height = 500;
        }
        $im2 = imagecrop($im, ['x' => 0, 'y' => 0, 'width' => $width, 'height' => $new_height]);
        return $im2;

    }

    // for gallery
    function forGallery($im){
        $width = 255;
        $height = 170;
        
        $im2 = imagecrop($im, ['x' => 0, 'y' => 0, 'width' => $width, 'height' => $height]);
        return $im2;

    }

    /**
    * Resize an image and keep the proportions
    */
    function resizeImage($filename, $max_width, $max_height)
    {
        list($orig_width, $orig_height) = getimagesize($filename);

        $width = $orig_width;
        $height = $orig_height;

        # taller
        if ($height > $max_height) {
            $width = ($max_height / $height) * $width;
            $height = $max_height;
        }

        # wider
        if ($width > $max_width) {
            $height = ($max_width / $width) * $height;
            $width = $max_width;
        }

        $image_p = imagecreatetruecolor($width, $height);

        list($createFrom, $imgt) = $this->imageCreateFrom(getimagesize($filename)[2]);
        $image = $createFrom($filename);

        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height);

        return $image_p;
    }
    
	function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
      
    function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

}
