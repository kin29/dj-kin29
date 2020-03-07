<?php


namespace App\Controller;

use App\Form\CreationFormType;
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
     * @return Response
     *
     * 初回時はSpotifyのauthorize画面orログイン画面となるはず
     * 初回時以外or認証許可/拒否後は、/?code=XXX or /?error=XXXXにリダイレクトする
     */
    public function index(): Response
    {
        if (isset($_GET['error'])) {
            return $this->render('default/auth_failure.html.twig');
        }

        if (!isset($_GET['code'])) {
            $this->authAndApiHandler->redirectAuth(); //redirect_uriにリダイレクトする
        }

        $form = $this->createForm(CreationFormType::class, null, [
            'action' => $this->generateUrl('create') . '?code=' . $_GET['code'],
        ]);

        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/create", name="create", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $form = $this->createForm(CreationFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            list($retTracks, $retArtists) = $this->authAndApiHandler->getTopTrack($data['artistNames']);
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
