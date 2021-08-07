<?php

namespace App\Service\Spotify;

use SpotifyWebAPI\SpotifyWebAPI;

class CreatePlaylistService
{
    private SpotifyWebAPI $spotifyWebAPI;

    public function __construct(SpotifyWebAPI $spotifyWebAPI)
    {
        $this->spotifyWebAPI = $spotifyWebAPI;
    }

    public function create(array $tracks, string $playlistName, bool $isPrivate = true): array
    {
        $playlist = $this->spotifyWebAPI->createPlaylist(['name' => $playlistName, 'public' => !$isPrivate]);
        $playlistId = $playlist->id;
        $this->spotifyWebAPI->addPlaylistTracks($playlistId, $tracks);
        $playlist = $this->spotifyWebAPI->getPlaylist($playlistId);

        return [
            'name' => $playlist->name,
            'url' => $playlist->external_urls->spotify,
            'image' => $playlist->images[0]->url,
        ];
    }
}
