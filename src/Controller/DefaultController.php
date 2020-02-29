<?php


namespace App\Controller;

use App\Form\ArtistNameListType;
use App\Service\Spotify\AuthorizationHandler as SpotifyAuth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/auth_failure", name="auth_failure")
     * @return Response
     */
    public function authFailure(): Response
    {
        return $this->render('default/auth_failure.html.twig');
    }

    /**
     * @Route("/create", name="create")
     * @return Response
     */
    public function create(Request $request): Response
    {
        $form = $this->createForm(ArtistNameListType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $artistNamesData = [
                $data['artistName1'],
                $data['artistName2'],
                $data['artistName3'],
                $data['artistName4'],
                $data['artistName5'],
            ];

            $artistNames = [];
            foreach ($artistNamesData as $data) {
                if($data) $artistNames[] = $data;
            }
            var_dump($artistNames);
            list($tracks, $artists) = $this->spotifyAuth->handleRequest();
            var_dump($tracks);
            var_dump($artists);exit;

            return $this->redirect($this->generateUrl('create_complete'));
            //renderは効かない
        }

        return $this->render('create/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/create/complete", name="create_complete")
     * @return Response
     */
    public function createComplete()
    {
        return $this->render('create/complete.html.twig', [
//                'tracks' => $tracks,
//                'artists' => $artists
        ]);
    }

    /**
     * @Route("/create/failure", name="create_failure")
     * @return Response
     */
    public function createFailure(): Response
    {
        return $this->render('create/failure.html.twig');
    }
}
