<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 11.05.17
 * Time: 16:08
 */
namespace AppBundle\EventListener;

use AppBundle\Core\DatedInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DateDoctrineListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // only act on some "DatedInterface" entity
        if (!$entity instanceof DatedInterface) {
            return;
        }

        $entity->setCreatedAt(new \DateTime());
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // only act on some "DatedInterface" entity
        if (!$entity instanceof DatedInterface) {
            return;
        }

        $entity->setUpdatedAt(new \DateTime());
    }
}