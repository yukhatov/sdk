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

class PublisherStatisticRepository extends EntityRepository
{
    public function findStatistic()
    {
        return $this->createQueryBuilder('s')
            ->select([/*'s.offerRequestsCount', 's.approveRequestsCount',*/ 'SUM(s.offerRequestsCount) as totalRequests', 'SUM(s.approveRequestsCount) as approveRequests', 'COUNT(s.id) as activationsCount', 'pl.domain'])
            ->join('s.publisher', 'p')
            ->join('p.platform', 'pl')
            ->groupBy('p.platform')
            ->getQuery()
            ->getResult();
    }
}