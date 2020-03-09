<?php


namespace App\Tests\Controller;


use App\Service\Spotify\AuthAndApiHandler;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function test_index(): void
    {
        $client = $this->createRequestClient();
        $client->request('GET', '/', ['code' => 'xxxx']);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Create playlist', $response->getContent());
    }

    public function test_index_エラーの時(): void
    {
        $client = $this->createRequestClient();
        $client->request('GET', '/', ['error' => 'access_denied']);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Re-Try', $response->getContent());
    }

    public function test_index_認可コードがない時(): void
    {
        $client = $this->createRequestClient();
        $client->request('GET', '/');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Authorization to Spotify', $response->getContent());
    }

    public function test_authSpotify(): void
    {
        $authAndApiHandler = $this->prophesize(AuthAndApiHandler::class);
        $authAndApiHandler->redirectAuth()->shouldBeCalled();

        $client = $this->createRequestClient();
        $client->getContainer()->set(AuthAndApiHandler::class, $authAndApiHandler->reveal());
        $client->request('GET', '/auth_spotify');
    }

//    public function test_create(): void
//    {
//        $form = [
//            ''
//        ];
//
//        $authAndApiHandler = $this->prophesize(AuthAndApiHandler::class);
//
//        $client = $this->createRequestClient();
//        $client->getContainer()->set(AuthAndApiHandler::class, $authAndApiHandler->reveal());
//        $client->request('POST', '/create', $form);
//    }

    /**
     * @return KernelBrowser
     */
    public function createRequestClient(): KernelBrowser
    {
        static::ensureKernelShutdown();

        return static::createClient();
    }
}
