<?php


namespace App\Controller;

use App\Form\ArtistNameListType;
use App\Service\Spotify\ArtistTopTrackGetter;
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
    /**
     * @var ArtistTopTrackGetter
     */
    private $artistTopTrackGetter;

    public function __construct(SpotifyAuth $spotifyAuth, ArtistTopTrackGetter $artistTopTrackGetter)
    {
        $this->spotifyAuth = $spotifyAuth;
        $this->artistTopTrackGetter = $artistTopTrackGetter;
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
     * @Route("/create", name="create_get", methods={"GET"})
     * @return Response
     */
    public function createGet()
    {
        if (isset($_GET['error'])) { // 認証拒否したら、?error=access_denied とかってパラメータがついてるはず
            return $this->render('default/auth_failure.html.twig');
        }

        $form = $this->createForm(ArtistNameListType::class, null, [
            // 'action' => $this->generateUrl('create_complete')
        ]);

        return $this->render('create/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/create", name="create", methods={"POST"})
     * @return Response
     */
    public function create(Request $request): Response
    {
        $form = $this->createForm(ArtistNameListType::class, null, [
           // 'action' => $this->generateUrl('create_complete')
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->artistTopTrackGetter->handleRequest();
            return $this->redirect($this->generateUrl('create_complete'));

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
//            list($tracks, $artists) = $this->artistTopTrackGetter->get($artistNames);
//            var_dump($tracks);
//            var_dump($artists);
//
            //return $this->redirect($this->generateUrl('create_complete'));
            //renderは効かない
        }

        return $this->render('create/failure.html.twig');
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
