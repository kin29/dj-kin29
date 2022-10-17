<?php

declare(strict_types=1);

namespace App\Service\Spotify;

use App\Service\Spotify\DTO\Artist;
use App\Service\Spotify\DTO\Track;
use SpotifyWebAPI\SpotifyWebAPI;

class GetTopTrackService
{
    public function __construct(private readonly SpotifyWebAPI $spotifyWebAPI)
    {
    }

    /**
     * @param array<string> $artistNames
     *
     * @return array{array<Track>, array<Artist>}
     */
    public function get(array $artistNames, string $type = 'artist'): array
    {
        $retTrackList = [];
        $retArtistList = [];
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
            $retArtistList[] = new Artist(
                $results->artists->items[0]->id,
                $artistName
            );

            /** @var object{tracks: array<object{id: string, name: string}>} $topTracks */
            $topTracks = $this->spotifyWebAPI->getArtistTopTracks($artistId, ['country' => 'JP']);
            foreach ($topTracks->tracks as $topTrack) {
                $retTrackList[] = new Track(
                    $topTrack->id,
                    $topTrack->name,
                );
            }
        }

        return [$retTrackList, $retArtistList];
    }
}
