<?php


namespace App\Service;


use SpotifyWebAPI;

class SpotifyAuthorizationHandler
{
    /**
     * @var SpotifyWebAPI\SpotifyWebAPI
     */
    private $api;

    /**
     * @var
     */
    private $session;

    public function __construct(SpotifyWebAPI\SpotifyWebAPI $api, SpotifyWebAPI\Session $session)
    {
        $this->api = $api;
        $this->session = $session;
    }

    public function handleRequest()
    {
        var_dump($_GET);exit;
        if (isset($_GET['code'])) {
            $this->session->requestAccessToken($_GET['code']);
            $this->api->setAccessToken($this->session->getAccessToken());

            print_r($this->api->me());
            //print_r($this->session->getAccessToken());
        } else {
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
}
