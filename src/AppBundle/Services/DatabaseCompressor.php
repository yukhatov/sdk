<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 06.06.17
 * Time: 15:10
 */

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

class DatabaseCompressor
{
    /**
     * @var EntityManager
     */
    private $em;
    
    /**
     * OfferProvider constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    /**
     * @return bool
     */
    public function compressRewards() :bool
    {
        $rewards = $this->em->getRepository('AppBundle:Reward')->findOutdated();

        if ($rewards) {
            foreach ($rewards as $reward) {
                $this->em->remove($reward);
            }

            $this->em->flush();

            return true;
        }

        return false;
    }
}