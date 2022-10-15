<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Service\Spotify\AuthHandler;
use App\Service\Spotify\CreatePlaylistService;
use App\Service\Spotify\DTO\Artist;
use App\Service\Spotify\DTO\CreatedPlaylist;
use App\Service\Spotify\DTO\Track;
use App\Service\Spotify\GetTopTrackService;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    use ProphecyTrait;

    public function test_index(): void
    {
        $client = $this->createRequestClient();
        $client->request('GET', '/', ['code' => 'xxxx']);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Create playlist', $response->getContent());
    }

    public function test_index_エラーの時(): void
    {
        $client = $this->createRequestClient();
        $client->request('GET', '/', ['error' => 'access_denied']);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Re-Try', $response->getContent());
    }

    public function test_index_認可コードがない時(): void
    {
        $client = $this->createRequestClient();
        $client->request('GET', '/');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Authorization to Spotify', $response->getContent());
    }

    public function test_authSpotify(): void
    {
        $authHandler = $this->prophesize(AuthHandler::class);
        $authHandler->redirectAuth()->shouldBeCalled();

        $client = $this->createRequestClient();
        $client->getContainer()->set(AuthHandler::class, $authHandler->reveal());
        $client->request('GET', '/auth_spotify');
    }

    public function test_createPlaylist(): void
    {
        $client = $this->createRequestClient();
        $client->disableReboot();

        /** @var AuthHandler|ObjectProphecy $authHandler */
        $authHandler = $this->prophesize(AuthHandler::class);
        /** @var GetTopTrackService|ObjectProphecy $getTopTrackService */
        $getTopTrackService = $this->prophesize(GetTopTrackService::class);
        $getTopTrackService->get(['artist-name1', 'artist-name2'])
            ->willReturn([
                $trackList = [new Track('dummy-track-id', 'dummy-track-name')],
                [new Artist('dummy-artist-id-1', 'artist-name1'), new Artist('dummy-artist-id-2', 'artist-name2')],
            ])
            ->shouldBeCalled();
        /** @var CreatePlaylistService|ObjectProphecy $createPlaylistService */
        $createPlaylistService = $this->prophesize(CreatePlaylistService::class);
        $createPlaylistService->create($trackList, 'playlist-name', true)->willReturn(
            new CreatedPlaylist(
                $playlistName= 'playlist-name',
                'https://localhost/url',
                'https://localhost/image',
            ),
        )->shouldBeCalled();

        $client->getContainer()->set(AuthHandler::class, $authHandler->reveal());
        $client->getContainer()->set(GetTopTrackService::class, $getTopTrackService->reveal());
        $client->getContainer()->set(CreatePlaylistService::class, $createPlaylistService->reveal());
        $crawler = $client->request('GET', '/', ['code' => 'xxxx']);
        $form = $crawler->filter('form')->form();
        $formValues = $form->getValues();
        $formValues['creation_form[artistNames][artistName1]'] = 'artist-name1';
        $formValues['creation_form[artistNames][artistName2]'] = 'artist-name2';
        $formValues['creation_form[playlistName]'] = $playlistName;

        $form->setValues($formValues);
        $client->submit($form);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('More Create！', $response->getContent());
    }

    public function createRequestClient(): KernelBrowser
    {
        static::ensureKernelShutdown();

        return static::createClient();
    }
}
