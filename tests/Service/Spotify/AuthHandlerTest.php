<?php

declare(strict_types=1);

namespace App\Tests\Service\Spotify;

use App\Service\Spotify\AuthHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class AuthHandlerTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|Session|null $sessionP;
    private ObjectProphecy|SpotifyWebAPI|null $spotifyWebApiP;

    protected function setUp(): void
    {
        $this->sessionP = $this->prophesize(Session::class);
        $this->spotifyWebApiP = $this->prophesize(SpotifyWebAPI::class);
    }

    protected function tearDown(): void
    {
        $this->sessionP = null;
        $this->spotifyWebApiP = null;
    }

    public function test_readyAccessToken(): void
    {
        $code = 'dummy-code';
        $this->sessionP->getAccessToken()
            ->willReturn($accessToken = 'dummy-access-token')
            ->shouldBeCalled();
        $this->sessionP->requestAccessToken($code)->shouldNotBeCalled();
        $this->spotifyWebApiP->setAccessToken($accessToken)->shouldBeCalled();

        $this->getSUT()->readyAccessToken($code);
    }

    public function test_readyAccessToken_accessTokenが空文字の時はrequestAccessTokenをする(): void
    {
        $code = 'dummy-code';
        $this->sessionP
            ->getAccessToken()
            ->willReturn('')
            ->shouldBeCalled();
        $this->sessionP->requestAccessToken($code)->shouldBeCalled();
        //$this->sessionP->getAccessToken()->willReturn($accessToken = 'dummy-access-token')->shouldBeCalled();
        $this->spotifyWebApiP->setAccessToken(Argument::any())->shouldBeCalled();

        $this->getSUT()->readyAccessToken($code);
    }

    public function getSUT(): AuthHandler
    {
        return new AuthHandler($this->sessionP->reveal(), $this->spotifyWebApiP->reveal());
    }
}
