<?php


namespace App\Controller;

use App\Form\ArtistNameListType;
use App\Service\SpotifyAuthorizationHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @var SpotifyAuthorizationHandler
     */
    private $spotifyAuth;

    public function __construct(SpotifyAuthorizationHandler $spotifyAuth)
    {
        $this->spotifyAuth = $spotifyAuth;
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $this->spotifyAuth->handleRequest();

        $form = $this->createForm(ArtistNameListType::class, null, [
            'action' => $this->generateUrl('post'),
        ]);

        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/post", name="post")
     */
    public function post()
    {
        return $this->render('default/post.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }
}
