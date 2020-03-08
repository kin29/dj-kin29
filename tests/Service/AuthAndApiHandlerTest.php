<?php


namespace App\Tests\Service;


use App\Service\Spotify\AuthAndApiHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use SpotifyWebAPI;
use Symfony\Component\Routing\RouterInterface;

class AuthAndApiHandlerTest extends TestCase
{
    /**
     * @var SpotifyWebAPI\SpotifyWebAPI|ObjectProphecy
     */
    private $api;

    /**
     * @var SpotifyWebAPI\Session|ObjectProphecy
     */
    private $session;

    /**
     * @var RouterInterface|ObjectProphecy
     */
    private $router;

    public function setUp()
    {
        $this->api = $this->prophesize(SpotifyWebAPI\SpotifyWebAPI::class);
        $this->session = $this->prophesize(SpotifyWebAPI\Session::class);
        $this->router = $this->prophesize(RouterInterface::class);
    }

    public function tearDown()
    {
        $this->api = null;
        $this->session = null;
        $this->router = null;
    }

    public function test_getTopTrack()
    {
        $artistNames = ['dummy-artist-name'];
        $_GET['code'] = 'dummy-code';

        $this->session->getAccessToken()->willReturn('')->shouldBeCalled();
        $this->session->requestAccessToken($_GET['code'])->shouldBeCalled();
        $this->api->setAccessToken(Argument::type('string'))->shouldBeCalled();

        $searchResult = $this->getSearchResultJson();
        $this->api->search($artistNames[0], $type = 'artist', array('limit' => 1))
            ->willReturn($searchResult)
            ->shouldBeCalled();
        $this->api->getArtistTopTracks($searchResult->artists->items[0]->id, ['country' => 'JP'])
            ->willReturn($this->getArtistTopTrackResultJson())
            ->shouldBeCalled();

        $SUT = $this->getSUT();
        $SUT->getTopTrack($artistNames);
    }

    /**
     * @return mixed
     * reference https://developer.spotify.com/documentation/web-api/reference/search/search/
     */
    private function getSearchResultJson()
    {
        $ret = [
            'artists' => [
                'items' => [
                    [
                        'id' => 1,
                        'name' => 'dummy-artist-name'
                    ],
                ]
            ]
        ];

        return json_decode(json_encode($ret));
    }

    /**
     * @return mixed
     * reference https://developer.spotify.com/documentation/web-api/reference/artists/get-artists-top-tracks/
     */
    private function getArtistTopTrackResultJson()
    {
        $ret = [
            'tracks' => [
                [
                    'id' => 1,
                    'name' => 'dummy-track-name1'
                ],
                [
                    'id' => 2,
                    'name' => 'dummy-track-name2'
                ],
            ]
        ];

        return json_decode(json_encode($ret));
    }

    public function test_makePlaylist()
    {
        $tracks = [];
        $playlistName = 'dummy-playlist-name';
        $_GET['code'] = 'dummy-code';

        $this->session->getAccessToken()->willReturn('dummy_access_token')->shouldBeCalled();
        $this->session->requestAccessToken($_GET['code'])->shouldNotBeCalled();
        $this->api->setAccessToken(Argument::type('string'))->shouldBeCalled();

        $playlistId = 123;
        $this->api->createPlaylist(['name' => $playlistName, 'public' => false])
            ->willReturn(json_decode(json_encode(['id' => $playlistId,])))
            ->shouldBeCalled();

        $this->api->addPlaylistTracks($playlistId, $tracks)->shouldBeCalled();
        $this->api->getPlaylist($playlistId)->willReturn($this->getPlaylistResultJson())->shouldBeCalled();

        $SUT = $this->getSUT();
        $SUT->makePlaylist($tracks, $playlistName);
    }

    /**
     * @return mixed
     * reference https://developer.spotify.com/documentation/web-api/reference/playlists/get-playlist/
     */
    private function getPlaylistResultJson()
    {
        $ret = [
            'name' => 123,
            'external_urls' => [
                'spotify' => 'http://open.spotify.com/user/spotify/playlist/123'
            ],
            'images' => [
                [
                    'url' => 'https://i.scdn.co/image/xxxx'
                ]
            ]
        ];

        return json_decode(json_encode($ret));
    }

    /**
     * @return AuthAndApiHandler
     */
    private function getSUT(): AuthAndApiHandler
    {
        return new AuthAndApiHandler(
            $this->api->reveal(),
            $this->session->reveal(),
            $this->router->reveal()
        );
    }
}
