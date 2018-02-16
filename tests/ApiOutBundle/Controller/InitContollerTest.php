<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 15.06.17
 * Time: 11:45
 */

namespace Tests\ApiOutBundle\Controller;

require 'vendor/autoload.php';

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InitControllerTest extends WebTestCase
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

        $this->application = $this->em
            ->getRepository('AppBundle:Application')
            ->findFirst();
    }
    
    public function testGetInit()
    {
        $this->client->request('GET', '/api/init');
        $this->assertTrue(403 === $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/api/init', [], [], [
            'HTTP_API-AUTH-TOKEN' => $this->application->getPublisher()->getApiToken(),
            'HTTP_APP-ID' => $this->application->getStoreAppId(),
            'HTTP_DEVICE-ID' => 'TEST_DEVICE'
        ]);
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
    }
}