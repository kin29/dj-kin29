<?php

namespace App\Tests\Service\Spotify;

use App\Service\Spotify\CreatePlaylistService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use SpotifyWebAPI\SpotifyWebAPI;

class CreatePlaylistServiceTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|SpotifyWebAPI $spotifyWebAPI;

    protected function setUp(): void
    {
        $this->spotifyWebAPI = $this->prophesize(SpotifyWebAPI::class);
    }

    public function testCreate(): void
    {
        $tracks = [];
        $playlistName = 'dummy-playlist-name';

        $playlistId = 123;
        $this->spotifyWebAPI->createPlaylist(['name' => $playlistName, 'public' => false])
            ->willReturn(json_decode(json_encode(['id' => $playlistId])))
            ->shouldBeCalled();

        $this->spotifyWebAPI->addPlaylistTracks($playlistId, $tracks)->shouldBeCalled();
        $this->spotifyWebAPI->getPlaylist($playlistId)->willReturn($this->getPlaylistResultJson())->shouldBeCalled();

        $SUT = new CreatePlaylistService($this->spotifyWebAPI->reveal());
        $SUT->create($tracks, $playlistName);
    }

    /**
     * @ref https://developer.spotify.com/documentation/web-api/reference/playlists/get-playlist/
     */
    private function getPlaylistResultJson(): mixed
    {
        $ret = [
            'name' => 123,
            'external_urls' => [
                'spotify' => 'http://open.spotify.com/user/spotify/playlist/123',
            ],
            'images' => [
                [
                    'url' => 'https://i.scdn.co/image/xxxx',
                ],
            ],
        ];

        return json_decode(json_encode($ret));
    }
}
