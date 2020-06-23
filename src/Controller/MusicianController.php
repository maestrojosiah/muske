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
use Knp\Snappy\Pdf;
use Knp\Snappy\Image;
use App\Repository\JobRepository;
use App\Service\OrganizeThings; 
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Repository\GalleryRepository;
use App\Repository\PdfThemeRepository;
use App\Repository\EducationRepository;
use App\Repository\DocumentRepository;
use App\Repository\PostRepository;

class MusicianController extends AbstractController
{
    private $snappy_pdf;
    private $snappy_img;
    
    public function __construct(Pdf $snappy_pdf, Image $snappy_img){
        $this->snappy_pdf = $snappy_pdf;
        $this->snappy_img = $snappy_img;
    }    


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
        
        // define email phone age and full name
        $skills = $musician->getSkills()[0];
        $education = $musician->getEducation()[0];
        $jobs = $musician->getJobs()[0];
        $roles = $musician->getJobstobeoffered()[0];

        $fields = [
                    "skills" => $musician->getSkills(),
                    "education" => $musician->getEducation(),
                    "jobs" => $musician->getJobs(),
                    "roles" => $musician->getJobstobeoffered()
                ];
        
        return $this->render('musician/new.html.twig', [
            'musician' => $musician,
            'fields' => $fields,
        ]);
    }

    /**
     * @Route("/{username}/{download}", name="musician_show", methods={"GET"})
     */
    public function show(
        MusicianRepository $musicianRepository, 
        GalleryRepository $galleryRepository, 
        PdfThemeRepository $pdfThemeRepository,
        PostRepository $postRepository,
        $username, $download = '', 
        SeoPageInterface $seoPage, 
        OrganizeThings $organizeThings
        ): Response
    {

        $musician = $musicianRepository->findOneByUsername($username);
        $jobsString = $this->getJobsAsString($musician);
        $skillsString = $this->getSkillsAsString($musician);

        if(count($musician->getUploadedphotos()) >= 3){
            $fourPhotos =  $galleryRepository->findFourPhotos($musician);
        } else {
            $fourPhotos = [];
        }
        
        $jobs = $organizeThings->organizedJobsAccordingToSettings($musician);
        $educ = $organizeThings->organizedEducationAccordingToSettings($musician);
        
        $seoPage
            ->setTitle($musician->getFullname()." | Resume")
            ->addMeta('name', 'keywords', $skillsString)
            ->addMeta('name', 'description', $musician->getAbout())
            ->addMeta('property', 'og:title', $musician->getFullname().' | Resume')
            ->addMeta('property', 'og:type', 'Resume')
            ->addMeta('property', 'og:url',  $this->generateUrl('musician_show', [
                'username' => $musician->getUsername() 
            ], true))
            ->addMeta('property', 'og:description', $jobsString)
        ;

        $abs_photourl = $this->getParameter('brochures_directory')."/thumbs/".$musician->getPhoto().".png";
        $photourl = "/uploads/photos/thumbs/".$musician->getPhoto().".png";
        $placeholder = "/img/headshot.jpg";
        $status = is_file($abs_photourl);
        $pdf_template = $musician->getPdfTheme() ? $musician->getPdfTheme() : 'simpleOne.html.twig' ;
        $theme = $pdfThemeRepository->findOneByTemplate($pdf_template);
        $themename = str_replace(" ", "_", $theme->getTitle());

        $thumbnailurl = strlen($musician->getPhoto()) > 1 ?  $photourl : $placeholder;
        
        $array_data = [
            'musician' => $musician,
            'jobs' => $jobs,
            'educ' => $educ,
            'fourPhotos' => $fourPhotos,
            'thumbnailurl' => $thumbnailurl,
        ];

        if($download == 'pdf'){
            if($status){
                return $this->toPdf("pdf/$pdf_template", $array_data, $musician->getUsername().'_'.$themename."_Resume");
            } else {
                return $this->redirectToRoute('error', ['error_msg' => "Broken links prevents the pdf from downloading. Upload your profile photo"]);
            }
        } elseif ($download == 'show') {
            $array_data += ['thumbnailurl' => $thumbnailurl];
            return $this->render("pdf/$pdf_template", $array_data);
        } else {
            $array_data += ['thumbnailurl' => $thumbnailurl];
            return $this->render('musician/show.html.twig', $array_data);
        }
        

    }

    /**
     * @Route("/musician/action/profile", name="musician_profile", methods={"GET"})
     */
    public function profile(
        JobRepository $jobRepository, 
        EducationRepository $educationRepository, 
        DocumentRepository $documentRepository,
        GalleryRepository $galleryRepository,
        PdfThemeRepository $pdfThemeRepository
        ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $musician = $this->getUser();

        if($musician->getFullname() == null){
            return $this->redirectToRoute('musician_new');
        }

        $data = [];

        $membership = $musician->getAccount();

        $roles_array = [];
        $skills_array = [];
        if($musician->getSettings()){
            
            // in case settings is not available
            $jobOrder = $musician->getSettings()->getJobOrder() ? $musician->getSettings()->getJobOrder() : "id";
            $jobSort = $musician->getSettings()->getJobOrderBy() ? $musician->getSettings()->getJobOrderBy() : "ASC";
            $jobs_array = $jobRepository->findByGivenField($jobOrder, $jobSort, $musician);
            foreach ($jobs_array as $key => $job) {
                if(count($job->getRoles()) > 0){
                    $roles_array[$key] = $job->getRoles();
                }
                
            }

            // in case settings is not available
            $eduOrder = $musician->getSettings()->getEduOrder() ? $musician->getSettings()->getEduOrder() : "id";
            $eduSort = $musician->getSettings()->getEduOrderBy() ? $musician->getSettings()->getEduOrderBy() : "ASC";
            $edu_array = $educationRepository->findByGivenField($eduOrder, $eduSort, $musician);
            foreach ($edu_array as $key => $edu) {
                if(count($edu->getSpecialties()) > 0){
                    $skills_array[$key] = $edu->getSpecialties();
                }
                
            }
    
        } 
        

        $doc_array = $documentRepository->findByGivenField("id", "ASC", $musician);
            
        $gallery_array = $galleryRepository->findByGivenField("id", "ASC", $musician);
        
        return $this->render('musician/profile.html.twig', [
            'musician' => $musician,
            'membership' => $membership,
            'roles' => $roles_array,
            'skills' => $skills_array,
            'documents' => $doc_array,
            'gallery' => $gallery_array,
            'pdfThemes' => $pdfThemeRepository->findAll(),
        ]);
    }


    /**
     * @Route("/musician/plan/select", name="musician_plan", methods={"GET"})
     */
    public function plan(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $musician = $this->getUser();

        if($musician->isMuskeAndActive() == 'true'){
            $membership = "Muske";
        } elseif($musician->isProAndActive() == 'true'){
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

        if($musician->isMuskeAndActive() == 'true'){
            $membership = "Muske";
        } elseif($musician->isProAndActive() == 'true'){
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
     * @Route("/musician/dompdf/test", name="dompdf_test")
     */
    // public function pdfGenerator($path = '', $array = [], $filename)
    // {
    //     // Configure Dompdf according to your needs
    //     $pdfOptions = new Options();
    //     $pdfOptions->set('defaultFont', 'Arial');
    //     $pdfOptions->set('isRemoteEnabled', TRUE);

    //     // Instantiate Dompdf with our options
    //     $dompdf = new Dompdf($pdfOptions);
    //     $contxt = stream_context_create([ 
    //         'ssl' => [ 
    //             'verify_peer' => FALSE, 
    //             'verify_peer_name' => FALSE,
    //             'allow_self_signed'=> TRUE
    //         ] 
    //     ]);
    //     $dompdf->setHttpContext($contxt);        
        
    //     // Retrieve the HTML generated in our twig file
    //     $html = $this->renderView($path, $array);
        
    //     // Load HTML to Dompdf
    //     $dompdf->loadHtml($html);
    //     $dompdf->set_option('isHtml5ParserEnabled', true);
    //     // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
    //     $dompdf->setPaper('A4', 'portrait');

    //     // Render the HTML as PDF
    //     $dompdf->render();

    //     // Output the generated PDF to Browser (force download)
    //     $dompdf->stream("$filename.pdf", [
    //         // "Attachment" => true
    //         "Attachment" => false
    //     ]);
    // }

    public function toPdf($path = '', $array = [], $filename): Response
    {

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView($path, $array);

        //Generate pdf with the retrieved HTML
        return new Response( $this->snappy_pdf->getOutputFromHtml($html), 200, array(
            'Content-Type'          => 'application/pdf',
            'Content-Disposition'   => 'inline; filename='.$filename.'.pdf'
        )
        );        
    }

    public function toImage($path = '', $array = [], $filename): Response
    {

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView($path, $array);

        //Generate pdf with the retrieved HTML
        return new Response( $this->snappy_img->getOutputFromHtml($html), 200, array(
            'Content-Type'          => 'image/jpg',
            'Content-Disposition'   => 'inline; filename='.$filename.'.jpg'
        )
        );        
    }

    public function getJobsAsString($musician){
        $jobs2bo = [];
        foreach ($musician->getJobsToBeOffered() as $job ) {
            $jobs2bo[] = $job->getJobtitle();
        }
        $jobs2bo[] = $musician->getSettings() ? $musician->getSettings()->getPlaceofwork() : '' ;
        $jobsString = implode(", ", $jobs2bo);

        return $jobsString;
    
    }

    public function getSkillsAsString($musician){
        $skills = [];
        foreach ($musician->getSkills() as $skill ) {
            $skills[] = $skill->getSkillname();
        }

        $skillsString = implode(", ", $skills);

        return $skillsString;
    
    }


}
