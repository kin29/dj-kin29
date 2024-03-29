<?php

declare(strict_types=1);

namespace App\Tests\Service\Spotify;

use App\Service\Spotify\GetTopTrackService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use SpotifyWebAPI\SpotifyWebAPI;

class GetTopTrackServiceTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|SpotifyWebAPI $spotifyWebApi;

    protected function setUp(): void
    {
        $this->spotifyWebApi = $this->prophesize(SpotifyWebAPI::class);
    }

    public function test_get(): void
    {
        $artistNames = ['dummy-artist-name'];

        $searchResult = $this->getSearchResultJson();
        $this->spotifyWebApi->search($artistNames[0], $type = 'artist', ['limit' => 1])
            ->willReturn($searchResult)
            ->shouldBeCalled();
        $this->spotifyWebApi->getArtistTopTracks($searchResult->artists->items[0]->id, ['country' => 'JP'])
            ->willReturn($this->getArtistTopTrackResultJson())
            ->shouldBeCalled();

        $SUT = new GetTopTrackService($this->spotifyWebApi->reveal());
        $SUT->get($artistNames);
    }

    /**
     * @return mixed
     * @see https://developer.spotify.com/documentation/web-api/reference/search/search/
     */
    private function getSearchResultJson(): mixed
    {
        $ret = [
            'artists' => [
                'items' => [
                    [
                        'id' => '1',
                        'name' => 'dummy-artist-name',
                    ],
                ],
            ],
        ];

        return json_decode(json_encode($ret));
    }

    /**
     * @ref https://developer.spotify.com/documentation/web-api/reference/artists/get-artists-top-tracks/
     */
    private function getArtistTopTrackResultJson(): mixed
    {
        $ret = [
            'tracks' => [
                [
                    'id' => '1',
                    'name' => 'dummy-track-name1',
                ],
                [
                    'id' => '2',
                    'name' => 'dummy-track-name2',
                ],
            ],
        ];

        return json_decode(json_encode($ret));
    }
}
