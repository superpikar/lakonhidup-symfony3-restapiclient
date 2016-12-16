<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class BlogController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        $postsResponse = $this->get('wordpressapi')->getPosts();
        $tagsResponse = $this->get('wordpressapi')->getTermsByTaxonomy('tag');
        $categoriesResponse = $this->get('wordpressapi')->getTermsByTaxonomy('category');
        // Display the result
        return $this->render('AppBundle:Blog:index.html.twig', array(
            'posts' => $postsResponse->body,
            'categories' => $categoriesResponse->body,
            'tags' => $tagsResponse->body
        ));
    }

    /**
     * @Route("/morePosts/{page}", name="more-posts")
     */
    public function morePostsAction($page)
    {
        $response = $this->get('wordpressapi')->getPosts($page);
        // Display the result
        return $this->render('AppBundle:Blog:ajax-post-list.html.twig', array(
            'posts' => $response->body
        ));
    }

    /**
     * @Route("/morePosts/{taxonomy}/{term}/{page}", name="more-posts-by-category")
     */
    public function morePostsByTaxonomyTermAction($taxonomy, $term, $page)
    {
        $response = $this->get('wordpressapi')->getPostsByTaxonomyTerm($taxonomy, $term, $page);
        // Display the result
        return $this->render('AppBundle:Blog:ajax-post-list.html.twig', array(
            'posts' => $response->body
        ));
    }

    /**
     * @Route("/post/{slug}", name="post")
     */
    public function postAction($slug)
    {
        $postResponse = $this->get('wordpressapi')->getPost($slug);
        $postsResponse = $this->get('wordpressapi')->getPosts();
        $commentsResponse = $this->get('wordpressapi')->getPostComments($postResponse->body->ID);
        return $this->render('AppBundle:Blog:post.html.twig', array(
            'post' => $postResponse->body,
            'posts' => $postsResponse->body,
            'comments' => $commentsResponse->body
        ));
    }

    /**
     * @Route("/{taxonomy}/{term}", name="posts-by-term")
     */
    public function postsByTermAction($taxonomy, $term)
    {
        $postsResponse = $this->get('wordpressapi')->getPostsByTaxonomyTerm($taxonomy, $term);
        $termsResponse = $this->get('wordpressapi')->getTermsByTaxonomy($taxonomy);
        $termResponse = $this->get('wordpressapi')->getTermByTaxonomy($taxonomy, $term);
        return $this->render('AppBundle:Blog:posts-by-term.html.twig', array(
            'type' => $taxonomy,
            'posts' => $postsResponse->body,
            'term' => $termResponse->body,
            'terms' => $termsResponse->body
        ));
    }
}
