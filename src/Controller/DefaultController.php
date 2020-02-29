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
     *
     * 初回時はauthorize画面となるはず
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
     * @Route("/auth_failure", name="auth_failure")
     */
    public function authFailure()
    {
        return $this->render('default/auth_failure.html.twig');
    }
}
