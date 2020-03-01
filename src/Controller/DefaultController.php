<?php


namespace App\Controller;

use App\Form\ArtistNameListType;
use App\Service\Spotify\AuthAndApiHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @var AuthAndApiHandler
     */
    private $authAndApiHandler;

    public function __construct(AuthAndApiHandler $authAndApiHandler)
    {
        $this->authAndApiHandler = $authAndApiHandler;
    }

    /**
     * @Route("/", name="index")
     *
     * 初回時はSpotifyのauthorize画面orログイン画面となるはず
     * 初回時以外or認証許可/拒否後は、/createにリダイレクトする
     */
    public function index(): void
    {
        $this->authAndApiHandler->handleRequest();
    }

    /**
     * @Route("/create", name="create_get", methods={"GET"})
     * @return Response
     */
    public function create()
    {
        if (isset($_GET['error'])) { // 認証拒否したら、?error=access_denied とかってパラメータがついてるはず
            return $this->render('default/auth_failure.html.twig');
        }
        $form = $this->createForm(ArtistNameListType::class);

        return $this->render('create/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/create", name="create", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function createPost(Request $request): Response
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
            foreach ($artistNamesData as $artistName) {
                if($artistName) $artistNames[] = $artistName;
            }
            list($retTracks, $retArtists) = $this->authAndApiHandler->getTopTrack($artistNames);
            $playListInfo = $this->authAndApiHandler->makePlaylist($retTracks, $data['playlistName'], $data['isPrivate']);

            return $this->render('create/complete.html.twig', [
                'name' => $playListInfo['name'],
                'url' => $playListInfo['url'],
                'created_artists' =>implode(' / ', $retArtists),
                'image' => $playListInfo['image'],
            ]);
        }

        return $this->render('create/failure.html.twig');
    }
}
