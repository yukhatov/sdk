<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 26.05.17
 * Time: 11:54
 */


namespace Tests\SkypeApiBundle\Controller;

require 'vendor/autoload.php';

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OfferControllerTest extends WebTestCase
{
    protected $em;
    protected $client;
    protected $publisher;

    protected function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->client = static::createClient();
        
        $this->publisher = $this->em
            ->getRepository('AppBundle:Publisher')
            ->findFirst();
    }
    
    public function testGetOffers()
    {
        $this->client->request('GET', '/skype_api/offers');
        $this->assertTrue(403 === $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/skype_api/offers', [
            'limit' => 1
        ], [], [
            'HTTP_API-AUTH-TOKEN' => $this->publisher->getApiToken(),
            'HTTP_SKYPE-ID' => $this->publisher->getSkypeId()
        ]);
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());

        //getting an id from response
        preg_match('/{"id":"(\d+)"/', $this->client->getResponse()->getContent(), $matches);
        $id = $matches[1] ?? '';

        $this->getOffer($id);
        $this->getOfferRequest($id);
    }

    private function getOffer($id)
    {
        $this->client->request('GET', '/skype_api/offers/' . $id);
        $this->assertTrue(403 === $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/skype_api/offers/' . $id, [], [], [
            'HTTP_API-AUTH-TOKEN' => $this->publisher->getApiToken(),
            'HTTP_SKYPE-ID' => $this->publisher->getSkypeId()
        ]);
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
    }

    private function getOfferRequest($id)
    {
        $this->client->request('GET', '/skype_api/offers/' . $id . '/request');
        $this->assertTrue(403 === $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/skype_api/offers/' . $id . '/request', [], [], [
            'HTTP_API-AUTH-TOKEN' => $this->publisher->getApiToken(),
            'HTTP_SKYPE-ID' => $this->publisher->getSkypeId()
        ]);
        $this->assertContains($this->client->getResponse()->getStatusCode(), [400, 200]);
    }
}