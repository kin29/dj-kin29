<?php


namespace App\Service\Spotify;

use SpotifyWebAPI;
use Symfony\Component\Routing\RouterInterface;

class AuthorizationHandler
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
        if (isset($_GET['error'])) { // 認証拒否したら、?error=access_denied とかってパラメータがついてるはず
            return $this->router->generate('auth_failure');
        }

        if (!isset($_GET['code'])) {
            $this->redirectAuth();
        }

        $this->session->requestAccessToken($_GET['code']);
        $this->api->setAccessToken($this->session->getAccessToken());
        //print_r($this->api->me());
        //print_r($this->session->getAccessToken());

        return $this->router->generate('create'). '?code=' . $_GET['code'];
    }

    /**
     * @param array $artistNames
     * @param string $type
     */
    public function get(array $artistNames, string $type = 'artist')
    {
        if (isset($_GET['error'])) { // 認証拒否したら、?error=access_denied とかってパラメータがついてるはず
            return $this->router->generate('auth_failure');
        }

        if (!isset($_GET['code'])) {
            $this->redirectAuth();
        }

        var_dump($this->api->me());
        $retTracks = [];
        $retArtists = [];
        foreach ($artistNames as $artistName) {
            $results = $this->api->search($artistName, $type, array('limit' => 1));
            var_dump($results);
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
