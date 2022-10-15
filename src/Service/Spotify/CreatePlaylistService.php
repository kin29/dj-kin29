<?php

declare(strict_types=1);

namespace App\Service\Spotify;

use App\Service\Spotify\DTO\CreatedPlaylist;
use App\Service\Spotify\DTO\Track;
use SpotifyWebAPI\SpotifyWebAPI;

class CreatePlaylistService
{
    public function __construct(private readonly SpotifyWebAPI $spotifyWebAPI)
    {
    }

    /**
     * @param array<Track> $trackList
     */
    public function create(array $trackList, string $playlistName, bool $isPrivate = true): CreatedPlaylist
    {
        /** @var object{id: string} $createdPlaylist */
        $createdPlaylist = $this->spotifyWebAPI->createPlaylist(['name' => $playlistName, 'public' => !$isPrivate]);
        $createdPlaylistId = $createdPlaylist->id;
        $trackIdList = [];
        foreach ($trackList as $track) {
            $trackIdList = $track->id;
        }
        $this->spotifyWebAPI->addPlaylistTracks($createdPlaylistId, $trackIdList);
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
