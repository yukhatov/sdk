<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 21.03.17
 * Time: 12:20
 */

namespace AppBundle\DataFixtures\ORM;
use AppBundle\Entity\UserPartner;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserPartnerData implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $userPartner = $this->newUserPartner($manager);
    }

    private function newUserPartner(ObjectManager $manager)
    {
        $encoder = $this->container->get('security.password_encoder');
        
        $userPartner = new UserPartner();

        $userPartner->setApiToken('appave.mobi.token');
        $userPartner->setUsername('appave');
        $userPartner->setUsernameCanonical('appave');
        $userPartner->setPassword($encoder->encodePassword($userPartner, $userPartner->getUsername()));
        $userPartner->setEmail('appave@gmail.com');
        $userPartner->setEmailCanonical('appave@gmail.com');
        $userPartner->setEnabled(true);

        $manager->persist($userPartner);
        $manager->flush();

        return $userPartner;
    }
}