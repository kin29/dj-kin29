<?php

declare(strict_types=1);

namespace App\Service\Spotify;

use SpotifyWebAPI\SpotifyWebAPI;

class CreatePlaylistService
{
    public function __construct(private readonly SpotifyWebAPI $spotifyWebAPI)
    {
    }

    /**
     * @param array<string> $trackIds
     *
     * @return array{name: string, url:string, image:string}
     */
    public function create(array $trackIds, string $playlistName, bool $isPrivate = true): array
    {
        /** @var object{id: string} $createdPlaylist */
        $createdPlaylist = $this->spotifyWebAPI->createPlaylist(['name' => $playlistName, 'public' => !$isPrivate]);
        $createdPlaylistId = $createdPlaylist->id;
        $this->spotifyWebAPI->addPlaylistTracks($createdPlaylistId, $trackIds);
        /**
         * @var object{
         *     name: string,
         *     external_urls: object{spotify: string},
         *     images: array<object{url: string}>
         *  } $playlist
         */
        $playlist = $this->spotifyWebAPI->getPlaylist($createdPlaylistId);

        return [
            'name' => $playlist->name,
            'url' => $playlist->external_urls->spotify,
            'image' => $playlist->images[0]->url,
        ];
    }
}
