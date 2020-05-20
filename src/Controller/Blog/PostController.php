<?php

namespace App\Controller\Blog;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BlogManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;

/**
 * @Route("/post")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="post_index", methods={"GET"})
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('blog/post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="post_new", methods={"GET","POST"})
     */
    public function new(Request $request, BlogManager $blogManager, SluggerInterface $slugger)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $musician = $this->getUser();
        $array = ['musician' => $musician, 'with_video' => false];
        return $blogManager->createNewEntity($request, $array, "Post", 'blog', "blog/post/new.html.twig", $slugger );
    }

    /**
     * @Route("/new/with-video", name="post_new_with_video", methods={"GET","POST"})
     */
    public function newWithVideo(Request $request, BlogManager $blogManager, SluggerInterface $slugger)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $musician = $this->getUser();
        $array = ['musician' => $musician, 'with_video' => true];
        return $blogManager->createNewEntity($request, $array, "Post", 'blog', "blog/post/new_with_vid.html.twig", $slugger );
    }


    /**
     * @Route("/{id}/{title}", name="post_show", methods={"GET"})
     */
    public function show(Post $post, PostRepository $postRepository, SeoPageInterface $seoPage): Response
    {
        $nextPost = $postRepository->getNextPost($post->getId(), $post->getMusician());
        $prevPost = $postRepository->getPrevPost($post->getId(), $post->getMusician());

        $seoPage
            ->setTitle($post->getTitle())
            ->addMeta('name', 'keywords', $post->getTags())
            ->addMeta('name', 'title', $post->getTitle())
            ->addMeta('name', 'description', substr($post->getContent(), 0, 100))
            ->addMeta('name', 'author', $post->getMusician()->getFullname())
            ->addMeta('property', 'og:title', $post->getTitle().' | Blog')
            ->addMeta('property', 'og:type', 'Blog Post')
            ->addMeta('property', 'og:url',  $this->generateUrl('blog', [
                'username' => $post->getMusician()->getUsername() 
            ], true))
            ->addMeta('property', 'og:description', substr($post->getContent(), 0 ,100))
        ;
        
        return $this->render('blog/post/show.html.twig', [
            'post' => $post,
            'musician' => $post->getMusician(),
            'nextPost' => $nextPost,
            'prevPost' => $prevPost,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="post_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, BlogManager $blogManager, SluggerInterface $slugger, Post $post): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $musician = $this->getUser();
        $array = ['musician' => $musician, 'with_video' => false];
        return $blogManager->createNewEntity($request, $array, "Post", 'blog', "blog/post/edit.html.twig", $slugger, 'edit', $post );
    }

    /**
     * @Route("/{id}/edit/with-video", name="post_edit_with_video", methods={"GET","POST"})
     */
    public function editWithVideo(Request $request, BlogManager $blogManager, SluggerInterface $slugger, Post $post): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $musician = $this->getUser();
        $array = ['musician' => $musician, 'with_video' => true];
        return $blogManager->createNewEntity($request, $array, "Post", 'blog', "blog/post/edit_with_vid.html.twig", $slugger, 'edit', $post );
    }

    /**
     * @Route("/{id}", name="post_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Post $post): Response
    {
        $musician = $post->getMusician();
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('blog', ['username' => $musician->getUsername()]);
    }
}
