<?php

namespace App\Service\Spotify;

use SpotifyWebAPI\SpotifyWebAPI;

class GetTopTrackService
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
    public function get(array $artistNames, string $type = 'artist')
    {
        $retTracks = [];
        $retArtists = [];
        foreach ($artistNames as $artistName) {
            $results = $this->spotifyWebAPI->search($artistName, $type, ['limit' => 1]);

            if (0 == count($results->artists->items)) {
                continue;
            }

            $artistId = $results->artists->items[0]->id;
            $tracks = $this->spotifyWebAPI->getArtistTopTracks($artistId, ['country' => 'JP'])->tracks;
            foreach ($tracks as $track) {
                $retTracks[] = $track->id;
            }
            $retArtists[] = $artistName;
        }

        return [$retTracks, $retArtists];
    }
}
