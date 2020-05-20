<?php

namespace App\Controller\Blog;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;
use App\Entity\Post;
use App\Form\PostType;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\MusicianRepository;
use Sonata\SeoBundle\Seo\SeoPageInterface;

class BlogController extends AbstractController
{
    /**
     * @Route("/{username}/blog/{page}", name="blog")
     */
    public function index(PostRepository $postRepository, MusicianRepository $musicianRepository, SeoPageInterface $seoPage, $username, $page = 1): Response
    {
        $limit = 10;
        $offset = $page * $limit - $limit;
        $data = [];

        $musician = $musicianRepository->findByUsername($username)[0];

        $posts = $postRepository
            ->findBy(
                array('musician' => $musician),
                array('id' => 'DESC'),
                $limit,
                $offset
            );

        $all_articles = $postRepository->findBy(
            array('musician' => $musician),
            array('id' => 'DESC')
        );
        $pages = count($all_articles) / $limit;

        if($posts){
            if($pages <= $page){
                $data['nextPage'] = $page;
            } else {
                $data['nextPage'] = $page + 1;
            }
            if($page > 1 ){
                $data['prevPage'] = $page -1;
            } else {
                $data['prevPage'] = 1;
            }
        } else {
            $data['nextPage'] = 1;
            $data['prevPage'] = 1;
        }
    
        $seoPage
            ->setTitle($musician->getFullname()." | Resume")
            ->addMeta('name', 'keywords', 'Blog Music Blog')
            ->addMeta('name', 'description', $musician->getAbout())
            ->addMeta('property', 'og:title', $musician->getFullname().' | Blog')
            ->addMeta('property', 'og:site_name', $musician->getFullname().' | Blog | Music Services Kenya')
            ->addMeta('property', 'og:type', 'Blog')
            ->addMeta('property', 'og:url',  $this->generateUrl('blog', [
                'username' => $musician->getUsername() 
            ], true))
            ->addMeta('property', 'og:description', $musician->getAbout())
        ;

        return $this->render('blog/home/index.html.twig', [
            'musician' => $musician,
            'posts' => $posts,
            'data' => $data,
            'all_articles' => $all_articles,
            'limit' => $limit,
        ]);
    }
}
