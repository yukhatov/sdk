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

class PlatformControllerTest extends WebTestCase
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
    
    public function testGetPlatforms()
    {
        $this->client->request('GET', '/skype_api/platforms');
        $this->assertTrue(403 === $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/skype_api/platforms', [
            'limit' => 1
        ], [], [
            'HTTP_API-AUTH-TOKEN' => $this->publisher->getApiToken(),
            'HTTP_SKYPE-ID' => $this->publisher->getSkypeId()
        ]);
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
    }
}