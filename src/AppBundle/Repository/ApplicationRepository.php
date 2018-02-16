<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 31.05.17
 * Time: 12:57
 */
namespace AppBundle\Repository;

use AppBundle\Entity\Application;
use Doctrine\ORM\EntityRepository;

class ApplicationRepository extends EntityRepository
{
    public function findPublishersApp(array $params) : Application
    {
        return  $this->createQueryBuilder('a')
            ->join('a.publisher', 'p')
            ->where('a.storeAppId = :storeAppId')
            ->andWhere('p.apiToken = :apiToken')
            ->setParameters([
                'storeAppId' => $params['app-id'] ?? '',
                'apiToken' => $params['api-auth-token'] ?? '',
            ])
            ->getQuery()
            ->getSingleResult();
    }

    public function findFirst()
    {
        return  $this->createQueryBuilder('a')
        ->setMaxResults(1)
        ->getQuery()
        ->getSingleResult();
    }
}
