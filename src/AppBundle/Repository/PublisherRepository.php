<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 21.03.17
 * Time: 11:58
 */
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class PublisherRepository extends EntityRepository
{
    public function findHighestId()
    {
        $id =  $this->createQueryBuilder('u')
            ->select('MAX(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
        
        return $id ? $id : 0;
    }

    public function findFirst()
    {
        return  $this->createQueryBuilder('u')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }
}