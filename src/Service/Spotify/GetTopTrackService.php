<?php

declare(strict_types=1);

namespace App\Service\Spotify;

use SpotifyWebAPI\SpotifyWebAPI;

class GetTopTrackService
{
    public function __construct(private readonly SpotifyWebAPI $spotifyWebAPI)
    {
    }

    /**
     * @param array<string> $artistNames
     *
     * @return array{array<string>, array<string>}
     */
    public function get(array $artistNames, string $type = 'artist'): array
    {
        $retTrackIds = [];
        $retArtistNames = [];
        foreach ($artistNames as $artistName) {
            /**
             * @var object{
             *      artists: object{
             *          items: array<object{id: string}>
             *     }
             * } $results
             */
            $results = $this->spotifyWebAPI->search($artistName, $type, ['limit' => 1]);
            if (0 === count($results->artists->items)) {
                continue;
            }
            $artistId = $results->artists->items[0]->id;
            $retArtistNames[] = $artistName;

            /** @var object{tracks: array<object{id: string}>} $topTracks */
            $topTracks = $this->spotifyWebAPI->getArtistTopTracks($artistId, ['country' => 'JP']);
            foreach ($topTracks->tracks as $topTrack) {
                $retTrackIds[] = $topTrack->id;
            }
        }

        return [$retTrackIds, $retArtistNames];
    }
}
