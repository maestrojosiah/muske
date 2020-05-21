<?php

namespace App\Controller\Blog;

use App\Entity\Reply;
use App\Form\ReplyType;
use App\Repository\ReplyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use ReCaptcha\ReCaptcha;

/**
 * @Route("/reply")
 */
class ReplyController extends AbstractController
{
    /**
     * @Route("/", name="reply_index", methods={"GET"})
     */
    public function index(ReplyRepository $replyRepository): Response
    {
        return $this->render('blog/reply/index.html.twig', [
            'replies' => $replyRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="reply_new", methods={"GET","POST"})
     */
    public function new(Request $request)
    {
        
        $recaptcha = new ReCaptcha('6LeFG_oUAAAAAKp2WYl_tACZd0Bvorf4D8RGTtsD');
        $resp = $recaptcha->verify($request->request->get('grecaptcha'), $request->getClientIp());   
        $message = $resp;
        if (!$resp->isSuccess()) {
            // Do something if the submit wasn't valid ! Use the message to show something
            $message = "The reCAPTCHA wasn't entered correctly. Go back and try it again.";
        } else {
            $data = [];
            $content = $this->sanitizeInput($request->request->get('reply'));
            $replier_name = $this->sanitizeInput($request->request->get('replier_name'));
            $replier_email = $this->sanitizeInput($request->request->get('replier_email'));
            $comment_id = $this->sanitizeInput($request->request->get('comment_id'));
            $submitted = new \DateTime("now");
            $comment = $this->getDoctrine()->getManager()->getRepository('App:Comment')->find($comment_id);

            $entityManager = $this->getDoctrine()->getManager();
            $reply = new Reply();
            $reply->setContent($content);
            $reply->setName($replier_name);
            $reply->setSubmitted($submitted);
            $reply->setEmail($replier_email);
            $reply->setComment($comment);
            $entityManager->persist($reply);
            $entityManager->flush();
            $data['name'] = $replier_name;
            $data['content'] = $content;
            $data['time'] = "just now";
           return new JsonResponse($data);
        }
        return new JsonResponse($message);
        
        

    }

    /**
     * @Route("/{id}", name="reply_show", methods={"GET"})
     */
    public function show(Reply $reply): Response
    {
        return $this->render('blog/reply/show.html.twig', [
            'reply' => $reply,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="reply_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Reply $reply): Response
    {
        $form = $this->createForm(ReplyType::class, $reply);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reply_index');
        }

        return $this->render('blog/reply/edit.html.twig', [
            'reply' => $reply,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reply_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Reply $reply): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reply->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reply);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reply_index');
    }

    public function sanitizeInput($input){
        $cleanInput = trim(strip_tags($input));
        return $cleanInput;

    }
    
}
