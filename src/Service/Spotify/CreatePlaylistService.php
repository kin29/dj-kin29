<?php

declare(strict_types=1);

namespace App\Service\Spotify;

use App\Service\Spotify\DTO\CreatedPlaylist;
use SpotifyWebAPI\SpotifyWebAPI;

class CreatePlaylistService
{
    public function __construct(private readonly SpotifyWebAPI $spotifyWebAPI)
    {
    }

    /**
     * @param array<string> $trackIds
     * @param string        $playlistName
     * @param bool          $isPrivate
     * @return CreatedPlaylist
     */
    public function create(array $trackIds, string $playlistName, bool $isPrivate = true): CreatedPlaylist
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

        return new CreatedPlaylist(
            $playlist->name,
            $playlist->external_urls->spotify,
            $playlist->images[0]->url,
        );
    }
}
