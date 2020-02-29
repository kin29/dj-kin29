<?php


namespace App\Service;


use SpotifyWebAPI;
use Symfony\Component\Routing\RouterInterface;

class SpotifyAuthorizationHandler
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
        if (isset($_GET['error'])) { // ?error=access_denied とかってパラメータがついてるはず
            return $this->router->generate('auth_failure');
        }

        if (isset($_GET['code'])) {
            $this->session->requestAccessToken($_GET['code']);
            $this->api->setAccessToken($this->session->getAccessToken());

            print_r($this->api->me());
            //print_r($this->session->getAccessToken());
            return $this->router->generate('create');
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
