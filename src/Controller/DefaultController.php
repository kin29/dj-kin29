<?php

namespace App\Controller;

use App\Form\CreationFormType;
use App\Service\Spotify\AuthHandler;
use App\Service\Spotify\CreatePlaylistService;
use App\Service\Spotify\GetTopTrackService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function __construct(
        private readonly AuthHandler $authHandler,
        private readonly GetTopTrackService $getTopTrackService,
        private readonly CreatePlaylistService $createPlaylistService
    ) {
    }

    // リダイレクトURL.
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        if ($request->query->get('error')) {
            return $this->render('default/auth_failure.html.twig');
        }

        $code = $request->query->get('code');
        if (null === $code) {
            return $this->render('default/index.html.twig');
        }

        $form = $this->createForm(CreationFormType::class, null, [
            'action' => $this->generateUrl('app_create_playlist').'?code='.$code,
        ]);

        return $this->render('default/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/auth_spotify', name: 'app_auth_spotify', methods: ['GET'])]
    public function authSpotify(): void
    {
        $this->authHandler->redirectAuth(); // app_indexにリダイレクトする
    }

    #[Route('/create', name: 'app_create_playlist', methods: ['POST'])]
    public function createPlaylist(Request $request): Response
    {
        $form = $this->createForm(CreationFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->authHandler->readyAccessToken();
            list($retTracks, $retArtists) = $this->getTopTrackService->get($data['artistNames']);
            $playListInfo = $this->createPlaylistService->create($retTracks, $data['playlistName'], $data['isPrivate']);

            return $this->render('create/complete.html.twig', [
                'name' => $playListInfo['name'],
                'url' => $playListInfo['url'],
                'created_artists' => implode(' / ', $retArtists),
                'image' => $playListInfo['image'],
            ]);
        }

        return $this->render('create/failure.html.twig');
    }
}
