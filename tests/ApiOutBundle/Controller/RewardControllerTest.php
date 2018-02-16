<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 15.06.17
 * Time: 16:49
 */

namespace Tests\ApiOutBundle\Controller;

require 'vendor/autoload.php';

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RewardControllerTest extends WebTestCase
{
    protected $em;
    protected $data;
    protected $cache;
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

        $this->cache = static::$kernel->getContainer()
            ->get('cache');

        $this->data = [
            'HTTP_API-AUTH-TOKEN' => $this->application->getPublisher()->getApiToken(),
            'HTTP_APP-ID' => $this->application->getStoreAppId(),
            'HTTP_DEVICE-ID' => 'TEST_DEVICE'
        ];
    }
    
    public function testGetRewards()
    {
        $this->cache->clear();
        $this->client->request('GET', '/api/rewards');
        $this->assertTrue(403 === $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/api/rewards', [], [], $this->data);
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());

        $rewardId = json_decode($this->client->getResponse()->getContent(), true)[0]['id'];
        $isRewarded1 = json_decode($this->client->getResponse()->getContent(), true)[0]['is_rewarded'];

        $this->patchReward($rewardId);
        $this->cache->clear();

        $this->client->request('GET', '/api/rewards', [], [], $this->data);
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());

        $isRewarded2 = json_decode($this->client->getResponse()->getContent(), true)[0]['is_rewarded'];

        $this->assertEquals($isRewarded1, !$isRewarded2);
    }

    private function patchReward($id)
    {
        $this->client->request('PATCH', '/api/rewards/' . $id, [], [], $this->data);
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
    }
}