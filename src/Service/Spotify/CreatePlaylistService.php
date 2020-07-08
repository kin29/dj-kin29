<?php

namespace App\Service\Spotify;

use SpotifyWebAPI\SpotifyWebAPI;

class CreatePlaylistService
{
    /**
     * @var SpotifyWebAPI
     */
    private $spotifyWebAPI;

    public function __construct(SpotifyWebAPI $spotifyWebAPI)
    {
        $this->spotifyWebAPI = $spotifyWebAPI;
    }

    /**
     * @return array
     */
    public function create(array $tracks, string $playlistName, bool $isPrivate = true)
    {
        $playlist = $this->spotifyWebAPI->createPlaylist(['name' => $playlistName, 'public' => !$isPrivate]);
        $playlistId = $playlist->id;
        $this->spotifyWebAPI->addPlaylistTracks($playlistId, $tracks);
        $playlist = $this->spotifyWebAPI->getPlaylist($playlistId);

        return  [
            'name' => $playlist->name,
            'url' => $playlist->external_urls->spotify,
            'image' => $playlist->images[0]->url,
        ];
    }
}
