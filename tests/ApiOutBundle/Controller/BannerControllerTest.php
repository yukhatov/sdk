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

class BannerControllerTest extends WebTestCase
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
    
    public function testGetBanners()
    {
        $this->client->request('GET', '/api/banners');
        $this->assertTrue(403 === $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/api/banners', [
            'platform' => 'Android'
        ], [], [
            'HTTP_API-AUTH-TOKEN' => $this->application->getPublisher()->getApiToken(),
            'HTTP_APP-ID' => $this->application->getStoreAppId(),
            'HTTP_DEVICE-ID' => 'TEST_DEVICE'
        ]);
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
    }
}