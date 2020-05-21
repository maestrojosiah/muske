<?php

namespace App\Controller\Blog;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use ReCaptcha\ReCaptcha;
use App\Updates\CommentReplyMailer;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/", name="comment_index", methods={"GET"})
     */
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('blog/comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="comment_new", methods={"GET","POST"})
     */
    public function new(Request $request, CommentReplyMailer $commentReplyMailer)
    {

        $recaptcha = new ReCaptcha('6LeFG_oUAAAAAKp2WYl_tACZd0Bvorf4D8RGTtsD');
        $resp = $recaptcha->verify($request->request->get('grecaptcha'), $request->getClientIp());   
        $message = $resp;
        if (!$resp->isSuccess()) {
            // Do something if the submit wasn't valid ! Use the message to show something
            $message = "The reCAPTCHA wasn't entered correctly. Go back and try it again." . "(reCAPTCHA said: " . $resp->error . ")";
        } else {
            $message = "Success";
            $data = [];
            $content = $this->sanitizeInput($request->request->get('comment'));
            $commenter_name = $this->sanitizeInput($request->request->get('commenter_name'));
            $commenter_email = $this->sanitizeInput($request->request->get('commenter_email'));
            $post_id = $this->sanitizeInput($request->request->get('post_id'));
            $submitted = new \DateTime("now");
            $post = $this->getDoctrine()->getManager()->getRepository('App:Post')->find($post_id);

            $entityManager = $this->getDoctrine()->getManager();
            $comment = new Comment();
            $comment->setContent($content);
            $comment->setName($commenter_name);
            $comment->setSubmitted($submitted);
            $comment->setEmail($commenter_email);
            $comment->setPost($post);
            $entityManager->persist($comment);
            $entityManager->flush();
            $data['name'] = $commenter_name;
            $data['content'] = $content;
            $data['time'] = "just now";
           
            // send email to the commenter email
            if($commentReplyMailer->sendEmailMessage($post->getMusician()->getEmail(), $post->getMusician()->getFullname(), $commenter_email, $commenter_name, $content, "Article")){
                // $this->addFlash('success', 'Notification mail was sent successfully');
                $message = "Email has ben sent successfully";
            }

        }
        return new JsonResponse($data);

    }

    /**
     * @Route("/{id}", name="comment_show", methods={"GET"})
     */
    public function show(Comment $comment): Response
    {
        return $this->render('blog/comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="comment_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comment_index');
        }

        return $this->render('blog/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Comment $comment): Response
    {
        $post = $comment->getPost();

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_show', ['id' => $post->getId(), 'title' => $post->getTitle()]);
    }

    public function sanitizeInput($input){
        $cleanInput = trim(strip_tags($input));
        return $cleanInput;

    }


}
