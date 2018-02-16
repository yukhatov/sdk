<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 31.05.17
 * Time: 12:57
 */
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserPartnerRepository extends EntityRepository
{
    public function findFirst()
    {
        return  $this->createQueryBuilder('u')
        ->setMaxResults(1)
        ->getQuery()
        ->getSingleResult();
    }
}
