<?php


namespace App\Service\Spotify;

use SpotifyWebAPI;
use Symfony\Component\Routing\RouterInterface;

class ArtistTopTrackGetter
{
    /**
     * @var SpotifyWebAPI\SpotifyWebAPI
     */
    private $api;

    /**
     * @var SpotifyWebAPI\Session
     */
    private $session;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        SpotifyWebAPI\SpotifyWebAPI $api,
        SpotifyWebAPI\Session $session,
        RouterInterface $router
    )
    {
        $this->api = $api;
        $this->session = $session;
        $this->router = $router;
    }

    public function handleRequest()
    {
        if (!isset($_GET['code'])) {
            $this->redirectAuth();
        }

        $this->session->requestAccessToken($_GET['code']);
        $this->api->setAccessToken($this->session->getAccessToken());
//        print_r($this->api->me());
//        print_r($this->session->getAccessToken());

        //return $this->router->generate('create_complete')/*. '?code=' . $_GET['code']*/;
    }

    /**
     * @param array $artistNames
     * @param string $type
     */
    public function get(array $artistNames, string $type = 'artist')
    {
        if (!isset($_GET['code'])) {
            $this->redirectAuth();
        }

        $this->session->requestAccessToken($_GET['code']); //これ必要だった！
        $this->api->setAccessToken($this->session->getAccessToken()); //これも必要だったっぽい！

        $retTracks = [];
        $retArtists = [];
        foreach ($artistNames as $artistName) {
            $results = $this->api->search($artistName, $type, array('limit' => 1));

            if (count($results->artists->items) == 0) continue;

            $artistId = $results->artists->items[0]->id;
            $tracks = $this->api->getArtistTopTracks($artistId, ['country' => 'JP'])->tracks;
            foreach ($tracks as $track) {
                $retTracks[] = $track->id;
            }
            $retArtists[] = $artistName;
        }

        return [$retTracks, $retArtists];
    }

    public function makePlaylist(array $tracks, string $playlistName, bool $isPublic = true)
    {
        if (!isset($_GET['code'])) {
            $this->redirectAuth();
        }

        $this->session->requestAccessToken($_GET['code']); //これ必要だった！
        $this->api->setAccessToken($this->session->getAccessToken()); //これも必要だったっぽい！

        $playlist = $this->api->createPlaylist(['name' => $playlistName, 'public' => $isPublic]);

        return $playlist->id;
//        $playlistId = $playlist->id;
//        $this->api->addPlaylistTracks($playlistId, $tracks);
//        $playlist = $this->api->getPlaylist($playlistId);
//
//        //todo ここの分割作業を別関数にしたい。
//        return  [
//            'name' => $playlist->name,
//            'url' => $playlist->external_urls->spotify,
//            'image' => $playlist->images[0]->url
//        ];
    }

    private function redirectAuth()
    {
        header('Location: ' . $this->session->getAuthorizeUrl(
            [
                'scope' => [
                    'playlist-read-private',
                    'playlist-modify-private',
                    'user-read-private',
                    'playlist-modify'
                ]
            ])
        );
        exit;
    }
}
