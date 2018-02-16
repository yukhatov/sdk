<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 26.05.17
 * Time: 11:54
 */


namespace Tests\ApiOutBundle\Controller;

require 'vendor/autoload.php';

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OfferControllerTest extends WebTestCase
{
    protected $em;
    protected $client;
    protected $application;

    protected function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->client = static::createClient();
        
        $this->loadFixtures();

        $this->application = $this->em
            ->getRepository('AppBundle:Application')
            ->findFirst();
    }
    
    public function testGetOffers()
    {
        $this->client->request('GET', '/api/offers');
        $this->assertTrue(403 === $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/api/offers', [
            'limit' => 1
        ], [], [
            'HTTP_API-AUTH-TOKEN' => $this->application->getPublisher()->getApiToken(),
            'HTTP_APP-ID' => $this->application->getStoreAppId(),
            'HTTP_DEVICE-ID' => 'TEST_DEVICE'
        ]);
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());

        //getting an id from response
        preg_match('/{"ID":"(\d+)"/', $this->client->getResponse()->getContent(), $matches);
        $id = $matches[1] ?? '';

        $this->getOffer($id);
    }

    private function getOffer($id)
    {
        $this->client->request('GET', '/api/offers/' . $id);
        $this->assertTrue(403 === $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/api/offers/' . $id, [], [], [
            'HTTP_API-AUTH-TOKEN' => $this->application->getPublisher()->getApiToken(),
            'HTTP_APP-ID' => $this->application->getStoreAppId(),
            'HTTP_DEVICE-ID' => 'TEST_DEVICE'
        ]);
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
    }

    private function loadFixtures()
    {
        $client = static::createClient();
        $loader = new \Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader($client->getContainer());
        $loader->loadFromDirectory(static::$kernel->locateResource('@AppBundle/DataFixtures/ORM'));
        $purger = new \Doctrine\Common\DataFixtures\Purger\ORMPurger($this->em);
        $executor = new \Doctrine\Common\DataFixtures\Executor\ORMExecutor($this->em, $purger);
        $executor->execute($loader->getFixtures());
    }
}