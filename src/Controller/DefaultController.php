<?php


namespace App\Controller;

use App\Form\ArtistNameListType;
use App\Service\SpotifyAuthorizationHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
     * 初回時はSpotifyのauthorize画面orログイン画面となるはず
     */
    public function index()
    {
        $this->spotifyAuth->handleRequest();
    }

    /**
     * @Route("/create", name="create")
     */
    public function create()
    {
        if (isset($_GET['error'])) { // ?error=access_denied とかってパラメータがついてるはず
            return $this->render('default/auth_failure.html.twig');
        }

        $form = $this->createForm(ArtistNameListType::class, null, [
            //todo 'action' => $this->generateUrl('post'),
        ]);

        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
