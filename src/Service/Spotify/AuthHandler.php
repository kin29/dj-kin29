<?php

namespace App\Service\Spotify;

use SpotifyWebAPI;

class AuthHandler
{
    public function __construct(
        private SpotifyWebAPI\Session $session,
        private SpotifyWebAPI\SpotifyWebAPI $spotifyWebApi
    ) {
    }

    public function redirectAuth(): void
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

    public function readyAccessToken(): void
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
