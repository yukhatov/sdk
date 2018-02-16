<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 16.06.17
 * Time: 15:04
 */

namespace Tests\ApiOutBundle\Controller;

require 'vendor/autoload.php';

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublisherControllerTest extends WebTestCase
{
    protected $em;
    protected $user;
    protected $client;
    protected $publisher;
    protected $application;

    protected function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->client = static::createClient();

        $this->user = $this->em
            ->getRepository('AppBundle:UserPartner')
            ->findFirst();

        $this->publisher = $this->em
            ->getRepository('AppBundle:Publisher')
            ->findFirst();

        $this->application = $this->em
            ->getRepository('AppBundle:Application')
            ->findFirst();
    }
    
    public function testGetPublisher()
    {
        $this->client->request('GET', '/partner_api/publishers/' . $this->publisher->getId());
        $this->assertTrue(403 === $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/partner_api/publishers/' . $this->publisher->getId(), [], [], [
            'HTTP_API-AUTH-TOKEN' => $this->user->getApiToken(),
        ]);
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
    }

    public function testPostPublisher()
    {
        $data = [
            'apiToken' => 'FsMxT4cfxWelmFgTArAfXfD0jZolq09i',
            'name' => $this->publisher->getName(),
            'domain' => 'api.tapgerine.net',
            'isSkypeBotActive' => 1
        ];

        $this->client->request('POST', '/partner_api/publishers', [], [], []);
        $this->assertTrue(403 === $this->client->getResponse()->getStatusCode());

        $this->client->request('POST', '/partner_api/publishers', [], [], [
            'HTTP_API-AUTH-TOKEN' => $this->user->getApiToken(),
        ]);
        $this->assertTrue(400 === $this->client->getResponse()->getStatusCode());

        $this->client->request('POST', '/partner_api/publishers', $data, [], [
            'HTTP_API-AUTH-TOKEN' => $this->user->getApiToken(),
        ]);
        $this->assertTrue(201 === $this->client->getResponse()->getStatusCode());

        $pubId = json_decode($this->client->getResponse()->getContent(), true)['id'];

        $this->client->request('POST', '/partner_api/publishers', $data, [], [
            'HTTP_API-AUTH-TOKEN' => $this->user->getApiToken(),
        ]);
        $this->assertTrue(400 === $this->client->getResponse()->getStatusCode());

        $this->deletePublisher($pubId);
    }

    public function testPatchPublisher()
    {
        $this->client->request('PATCH', '/partner_api/publishers/' . $this->publisher->getId(), [], [], []);
        $this->assertTrue(403 === $this->client->getResponse()->getStatusCode());

        $this->client->request('PATCH', '/partner_api/publishers/' . $this->publisher->getId(), [], [], [
            'HTTP_API-AUTH-TOKEN' => $this->user->getApiToken(),
        ]);
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
    }

    private function deletePublisher($id)
    {
        $this->client->request('DELETE', '/partner_api/publishers/' . $id, [], [], []);
        $this->assertTrue(403 === $this->client->getResponse()->getStatusCode());

        $this->client->request('DELETE', '/partner_api/publishers/' . $id, [], [], [
            'HTTP_API-AUTH-TOKEN' => $this->user->getApiToken(),
        ]);
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
    }
}
