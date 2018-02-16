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

class ApplicationControllerTest extends WebTestCase
{
    protected $em;
    protected $user;
    protected $cache;
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

        $this->cache = static::$kernel->getContainer()
            ->get('cache');
    }
    
    public function testGetApplication()
    {
        $this->client->request('GET', '/partner_api/applications/' . $this->application->getId());
        $this->assertTrue(403 === $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/partner_api/applications/' . $this->application->getId(), [], [], [
            'HTTP_API-AUTH-TOKEN' => $this->user->getApiToken(),
        ]);
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
    }

    public function testPostApplication()
    {
        $data = [
            'publisherId' => $this->publisher->getId(),
            'isSandBox' => 1,
            'storeAppId' => 'test.test',
            'virtualCurrency' => 'Coins',
            'isRewarded' => 1,
            'isQuickReward' => 0,
            'exchangeRate' => 100
        ];

        $this->client->request('POST', '/partner_api/applications', [], [], []);
        $this->assertTrue(403 === $this->client->getResponse()->getStatusCode());

        $this->client->request('POST', '/partner_api/applications', [], [], [
            'HTTP_API-AUTH-TOKEN' => $this->user->getApiToken(),
        ]);
        $this->assertTrue(400 === $this->client->getResponse()->getStatusCode());

        $this->client->request('POST', '/partner_api/applications', $data, [], [
            'HTTP_API-AUTH-TOKEN' => $this->user->getApiToken(),
        ]);
        $this->assertTrue(201 === $this->client->getResponse()->getStatusCode());

        $appId = json_decode($this->client->getResponse()->getContent(), true)['id'];

        $this->client->request('POST', '/partner_api/applications', $data, [], [
            'HTTP_API-AUTH-TOKEN' => $this->user->getApiToken(),
        ]);
        $this->assertTrue(400 === $this->client->getResponse()->getStatusCode());

        $this->deleteApplication($appId);
    }

    public function testPatchApplication()
    {
        $this->client->request('PATCH', '/partner_api/applications/' . $this->application->getId(), [], [], []);
        $this->assertTrue(403 === $this->client->getResponse()->getStatusCode());

        $this->client->request('PATCH', '/partner_api/applications/' . $this->application->getId(), [
            'isSandBox' => intval(!$this->application->getIsSandBox())
        ], [], [
            'HTTP_API-AUTH-TOKEN' => $this->user->getApiToken(),
        ]);
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/partner_api/applications/' . $this->application->getId(), [], [], [
            'HTTP_API-AUTH-TOKEN' => $this->user->getApiToken(),
        ]);

        $isSandbox = json_decode($this->client->getResponse()->getContent(), true)['is_sand_box'];

        $this->assertEquals($isSandbox, !$this->application->getIsSandBox());
    }

    private function deleteApplication($id)
    {
        $this->client->request('DELETE', '/partner_api/applications/' . $id, [], [], []);
        $this->assertTrue(403 === $this->client->getResponse()->getStatusCode());

        $this->client->request('DELETE', '/partner_api/applications/' . $id, [], [], [
            'HTTP_API-AUTH-TOKEN' => $this->user->getApiToken(),
        ]);
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
    }
}
