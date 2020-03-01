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

//        var_dump($_GET['code']);
//        var_dump($this->session->getAccessToken()); //null...
//        var_dump($this->session->getRefreshToken());//null...
//        var_dump($this->session->requestAccessToken($_GET['code']));

        $this->api->setAccessToken($this->session->getAccessToken());
        $results = $this->api->me();

        //$results = $this->api->search($artistNames[0], $type, array('limit' => 1));
//        return $this->session->getAccessToken();
//        $retTracks = [];
//        $retArtists = [];
//        foreach ($artistNames as $artistName) {
//            $results = $this->api->search($artistName, $type, array('limit' => 1));
//
//            if (count($results->artists->items) == 0) continue;
//
//            $artistId = $results->artists->items[0]->id;
//            $tracks = $this->api->getArtistTopTracks($artistId, ['country' => 'JP'])->tracks;
//            foreach ($tracks as $track) {
//                $retTracks[] = $track->id;
//            }
//            $retArtists[] = $artistName;
//        }
//
//        return [$retTracks, $retArtists];
        return $results;
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
