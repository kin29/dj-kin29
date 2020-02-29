<?php


namespace App\Controller;

use App\Form\ArtistNameListType;
use App\Service\Spotify\AuthorizationHandler as SpotifyAuth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @var SpotifyAuth
     */
    private $spotifyAuth;

    public function __construct(SpotifyAuth $spotifyAuth)
    {
        $this->spotifyAuth = $spotifyAuth;
    }

    /**
     * @Route("/", name="index")
     * @return RedirectResponse
     *
     * 初回時はSpotifyのauthorize画面orログイン画面となるはず
     */
    public function index(): RedirectResponse
    {
        return new RedirectResponse($this->spotifyAuth->handleRequest());
    }

    /**
     * @Route("/create", name="create")
     * @return Response
     */
    public function create(): Response
    {
        $form = $this->createForm(ArtistNameListType::class, null, [
            //todo 'action' => $this->generateUrl('post'),
        ]);

        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/auth_failure", name="auth_failure")
     * @return Response
     */
    public function authFailure(): Response
    {
        return $this->render('default/auth_failure.html.twig');
    }
}
