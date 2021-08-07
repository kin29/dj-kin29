<?php

namespace App\Tests\Service\Spotify;

use App\Service\Spotify\AuthHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class AuthHandlerTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|Session $session;
    private ObjectProphecy|SpotifyWebAPI $spotifyWebApi;

    protected function setUp(): void
    {
        $this->session = $this->prophesize(Session::class);
        $this->spotifyWebApi = $this->prophesize(SpotifyWebAPI::class);
    }

    public function testReadyAccessToken(): void
    {
        $_GET['code'] = 'dummy-code';
        $this->session->getAccessToken()->willReturn($accessToken = 'dummy-access-token')->shouldBeCalled();
        $this->session->requestAccessToken($_GET['code'])->shouldNotBeCalled();
        $this->spotifyWebApi->setAccessToken($accessToken)->shouldBeCalled();

        $this->getSUT()->readyAccessToken();
    }

    public function testReadyAccessToken_accessTokenNull(): void
    {
        $_GET['code'] = 'dummy-code';
        $this->session->getAccessToken()->willReturn('')->shouldBeCalled();
        $this->session->requestAccessToken($_GET['code']);
        $this->session->getAccessToken()->willReturn($accessToken = 'dummy-access-token')->shouldBeCalled();
        $this->spotifyWebApi->setAccessToken($accessToken)->shouldBeCalled();

        $this->getSUT()->readyAccessToken();
    }

    public function getSUT(): AuthHandler
    {
        return new AuthHandler($this->session->reveal(), $this->spotifyWebApi->reveal());
    }
}
