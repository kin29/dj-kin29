<?php

declare(strict_types=1);

namespace App\Tests\Service\Spotify;

use App\Service\Spotify\CreatePlaylistService;
use App\Service\Spotify\DTO\Track;
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

    public function test_create(): void
    {
        $trackList = [
            new Track($trackId1 = 'dummy-track-id-1', $trackName1 = 'dummy-track-name-1'),
            new Track($trackId2 = 'dummy-track-id-2', $trackName2 = 'dummy-track-name-2'),
        ];
        $playlistName = 'dummy-playlist-name';

        $playlistId = 123;
        $this->spotifyWebAPI->createPlaylist(['name' => $playlistName, 'public' => false])
            ->willReturn(json_decode(json_encode(['id' => $playlistId])))
            ->shouldBeCalled();

        $this->spotifyWebAPI
            ->addPlaylistTracks($playlistId, [$trackId1, $trackId2])
            ->shouldBeCalled();
        $this->spotifyWebAPI->getPlaylist($playlistId)
            ->willReturn($this->getPlaylistResultJson($playlistName))
            ->shouldBeCalled();

        $SUT = new CreatePlaylistService($this->spotifyWebAPI->reveal());
        $actual = $SUT->create($trackList, $playlistName);
        $this->assertSame($playlistName, $actual->name);
        $this->assertSame('https://open.spotify.com/user/spotify/playlist/123', $actual->url);
        $this->assertSame('https://i.scdn.co/image/xxxx', $actual->imageUrl);
    }

    /**
     * @ref https://developer.spotify.com/documentation/web-api/reference/playlists/get-playlist/
     */
    private function getPlaylistResultJson(string $playlistName): mixed
    {
        $ret = [
            'name' => $playlistName,
            'external_urls' => [
                'spotify' => 'https://open.spotify.com/user/spotify/playlist/123',
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
