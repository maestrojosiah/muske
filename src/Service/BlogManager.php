<?php 

namespace App\Service;

use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Entity\Reply;
use App\Form\ReplyType;
use App\Repository\ReplyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class BlogManager extends AbstractController
{
    private $submitted;
    private $name;
    private $email;
    private $content;
    private $image;
    private $title;
    private $entity;
    private $route_name;

    public function __construct()
    {
        // $this->jobs = new ArrayCollection();
        // $this->educ = new ArrayCollection();
    }


    public function createNewEntity($request, $array, $type, $redirect_to, $template, $slugger, $task = 'new', $entity = [] ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $musician = $this->getUser();

        if($task == 'new') {
            switch ($type) {
                case 'Post':
                    $entity = new Post();
                    $form = $this->createForm(PostType::class, $entity, ['with_video' => $array['with_video']]);
                    break;
                case 'Comment':
                    $entity = new Comment();
                    $form = $this->createForm(CommentType::class, $entity);
                    break;
                case 'Reply':
                    $entity = new Reply();
                    $form = $this->createForm(PostType::class, $entity);
                    break;
            }
    
        } else {
            switch ($type) {
                case 'Post':
                    $form = $this->createForm(PostType::class, $entity, ['with_video' => $array['with_video']]);
                    break;
                case 'Com
                ment':
                    $form = $this->createForm(CommentType::class, $entity);
                    break;
                case 'Reply':
                    $form = $this->createForm(PostType::class, $entity);
                    break;
            }

        }
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($type == "Post"){
                /** @var UploadedFile $imageFile */
                $imageFile = $form->get('image')->getData();

                // this condition is needed because the 'image' field is not required
                // so the image file must be processed only when a file is uploaded
                if ($imageFile) {

                    if(@is_array(getimagesize($imageFile))){
                        $image = true;
                    } else {
                        $image = false;
                    }
                    if($image == true) {
                        //delete the current photo if available
                        if($entity->getImage() != null ) {
                            $current_image_path = $this->getParameter('blog_directory')."/".$entity->getImage();
                            $current_image_thumb_path = $this->getParameter('blog_directory')."/thumbs/".$entity->getImage();
                            if(file_exists($current_image_path)){ unlink($current_image_path); }
                            if(file_exists($current_image_thumb_path)){ unlink($current_image_thumb_path); }
                        }
                    
                        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                        // this is needed to safely include the file name as part of the URL
                        $safeFilename = $slugger->slug($originalFilename);
                        $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                        // Move the file to the directory where brochures are stored
                        try {
                            $imageFile->move(
                                $this->getParameter('blog_directory'),
                                $newFilename
                            );
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }

                    } else {
                        $newFilename = $imageFile;
                    }
                    // updates the 'imageFilename' property to store the PDF file name
                    // instead of its contents
                    $entity->setMusician($musician);
                    $entity->setImage($newFilename);

                    $updir = $this->getParameter('blog_directory');
                    $image == true ? $img = $entity->getImage() : $img = "play_thumbnail.png";
                    $this->makeThumbnails($updir, $img);
                }

            }
            $submitted = new \DateTime("now");
            $entity->setSubmitted($submitted);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($entity);
            $entityManager->flush();

            return $this->redirectToRoute($redirect_to, ['musician' => $array['musician'], 'username' => $musician->getUsername()]);
        }
        if($task = 'edit'){
            return $this->render("$template", [
                'post' => $entity,
                'form' => $form->createView(),
                'musician' => $musician,
                ]);    
        }
        return $this->render("$template", [
            'musician' => $musician,
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    public function edit(Request $request, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('blog/post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    public function show(Post $post): Response
    {
        return $this->render('blog/post/show.html.twig', [
            'post' => $post,
        ]);
    }

    public function delete(Request $request, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_index');
    }

    public function makeThumbnails($updir, $img)
    {

        $thumbnail_width = 400;
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


}