<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 21.03.17
 * Time: 12:20
 */

namespace AppBundle\DataFixtures\ORM;
use AppBundle\Entity\Application;
use AppBundle\Entity\Platform;
use AppBundle\Entity\Publisher;
use AppBundle\Entity\PublisherStatistic;
use AppBundle\Entity\Reward;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPublisherData implements FixtureInterface, ContainerAwareInterface
{
    private $container;
    private $manager;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $platform2 = $this->newPlatform('api.absolute294200.loove.co');
        $publisher3 = $this->newPublisher($platform2, 'kWCuAs6bl8SC1oYPfINXZB75J6ZjXbsc', 'Eddard', 'test');
        $publisher2 = $this->newPublisher($platform2, 'REy4vzP44IvqmaEaq7R6EsHphPYrYZaJ', 'Tirion Lanister');
        $application2 = $this->newApplication($publisher2, false, 'com.ministone.game.risingsuperchef2');
        $application4 = $this->newApplication($publisher2, false, 'com.autobuild.app');
        $application3 = $this->newApplication($publisher3, false, 'com.freevpn.vpn_master');
        $publisherStat3 = new PublisherStatistic($publisher3);
        $publisherStat2 = new PublisherStatistic($publisher2);
        $this->newReward($application2);
        $this->newReward($application2);
        $this->newReward($application3);

        $this->manager->persist($publisherStat3);
        $this->manager->persist($publisherStat2);
        $this->manager->flush();
    }

    private function newPlatform(string $domain)
    {
        $platform = new Platform();

        $platform->setDomain($domain);

        $this->manager->persist($platform);

        return $platform;
    }

    private function newPublisher(Platform $platform, string $token, $name, $skypeId = null)
    {
        $user = new Publisher($this->container->get('security.password_encoder'), rand(0, 100));
        
        $user->setApiToken($token);
        $user->setPlatform($platform);
        $user->setName($name);
        $user->setSkypeId($skypeId);
        $user->setIsSkypeBotActive(1);

        $this->manager->persist($user);

        return $user;
    }

    private function newApplication(Publisher $publisher, $isSandBox, $appId)
    {
        $app = new Application();
        $app->setPublisher($publisher);
        $app->setIsSandBox($isSandBox);
        $app->setStoreAppId($appId);
        $app->setVirtualCurrency('Coins');
        $app->setIsRewarded(1);
        $app->setIsQuickReward(0);
        $app->setExchangeRate(100);

        $this->manager->persist($app);

        return $app;
    }

    private function newReward(Application $application)
    {
        $reward = new Reward();
        $reward->setApplication($application);
        $reward->setDeviceId('TEST_DEVICE');
        $reward->setIsRewarded(0);
        $reward->setAmount(rand(50, 150));
        $reward->setOfferId(rand(111000, 123000));
        $reward->setOfferPayout(rand(100, 200));

        $this->manager->persist($reward);

        return $reward;
    }
}