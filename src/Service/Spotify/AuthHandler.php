<?php

declare(strict_types=1);

namespace App\Service\Spotify;

use SpotifyWebAPI;

class AuthHandler
{
    public function __construct(
        private readonly SpotifyWebAPI\Session $session,
        private readonly SpotifyWebAPI\SpotifyWebAPI $spotifyWebApi
    ) {
    }

    public function redirectAuth(): void
    {
        header('Location: '.$this->session->getAuthorizeUrl(
                [
                    'scope' => [
                        'playlist-read-private', // getPlaylistに必要
                        'playlist-modify-private', // createPlaylist,addPlaylistTracksに必要
                        'playlist-modify-public', // createPlaylist,addPlaylistTracksに必要
                        'user-read-private',  // searchで必要
                    ],
                ])
        );

        exit;
    }

    public function readyAccessToken(string $code): void
    {
        if ('' === $code) {
            $this->redirectAuth();

            return;
        }

        if ('' === $this->session->getAccessToken()) {
            $this->session->requestAccessToken($code);
        }
        $this->spotifyWebApi->setAccessToken($this->session->getAccessToken());
    }
}
