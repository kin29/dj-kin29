<?php

namespace App\Service\Spotify;

use SpotifyWebAPI;

class AuthHandler
{
    /**
     * @var SpotifyWebAPI\Session
     */
    private $session;
    /**
     * @var SpotifyWebAPI\SpotifyWebAPI
     */
    private $spotifyWebApi;

    public function __construct(SpotifyWebAPI\Session $session, SpotifyWebAPI\SpotifyWebAPI $spotifyWebApi)
    {
        $this->session = $session;
        $this->spotifyWebApi = $spotifyWebApi;
    }

    public function redirectAuth()
    {
        header('Location: '.$this->session->getAuthorizeUrl(
                [
                    'scope' => [
                        'playlist-read-private', //getPlaylistに必要
                        'playlist-modify-private', //createPlaylist,addPlaylistTracksに必要
                        'playlist-modify-public', //createPlaylist,addPlaylistTracksに必要
                        'user-read-private',  //searchで必要
                    ],
                ])
        );

        exit;
    }

    public function readyAccessToken()
    {
        if (!isset($_GET['code'])) {
            $this->redirectAuth();
        }

        if ('' === $this->session->getAccessToken()) {
            $this->session->requestAccessToken($_GET['code']);
        }
        $this->spotifyWebApi->setAccessToken($this->session->getAccessToken());
    }
}
