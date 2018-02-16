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

class PostbackControllerTest extends WebTestCase
{
    protected $em;
    protected $client;
    protected $transactionIdGenerator;

    protected function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->transactionIdGenerator = static::$kernel->getContainer()
            ->get('transaction.id.generator');
        $this->client = static::createClient();
    }
    
    public function testGetPostback()
    {
        $application = $this->em
            ->getRepository('AppBundle:Application')
            ->findFirst();
        $click_id = $this->transactionIdGenerator->generate($application, 'TEST_DEVICE', 12345, 100, 100);
        
        $this->client->request('GET', '/partner_api/postbacks/1');
        $this->assertTrue(403 === $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/partner_api/postbacks/1', [], [], [
            'HTTP_API-AUTH-TOKEN' => 'appave.mobi.token',
        ]);
        $this->assertTrue(400 === $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/partner_api/postbacks/' . $click_id, [], [], [
            'HTTP_API-AUTH-TOKEN' => 'appave.mobi.token',
        ]);
        $this->assertTrue(201 === $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/partner_api/postbacks/' . $click_id, [], [], [
            'HTTP_API-AUTH-TOKEN' => 'appave.mobi.token',
        ]);
        $this->assertTrue(400 === $this->client->getResponse()->getStatusCode());
    }
}