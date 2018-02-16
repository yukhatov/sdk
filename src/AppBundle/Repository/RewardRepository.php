<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 21.03.17
 * Time: 11:58
 */
namespace AppBundle\Repository;

use AppBundle\Entity\Reward;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;

class RewardRepository extends EntityRepository
{
    public function findByParams(array $params) : array
    {
        $query = $this->createQueryWithParams($params);

        return $query->getQuery()->getResult();
    }

    public function findOneByParamsAndId(array $params, int $id) : Reward
    {
        $query = $this->createQueryWithParams($params);

        return $query
            ->andWhere('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    private function createQueryWithParams(array $params) : QueryBuilder
    {
        if (isset($params['isRewarded'])) {
            $isRewarded = [$params['isRewarded']];
        } else {
            $isRewarded = [1, 0];
        }

        $query =  $this->createQueryBuilder('r')
            ->join('r.application', 'a')
            ->join('a.publisher', 'p')
            ->where('r.isRewarded IN (:isRewarded)')
            ->andWhere('p.apiToken = :apiToken')
            ->andWhere('a.storeAppId = :storeAppId')
            ->andWhere('r.deviceId = :deviceId')
            ->setParameters([
                'storeAppId' => $params['app-id'] ?? '',
                'deviceId' => $params['device-id'] ?? '',
                'apiToken' => $params['api-auth-token'] ?? '',
                'isRewarded' => $isRewarded
            ]);

        return $query;
    }

    public function findOutdated()
    {
        $date = new \DateTime('now');
        $date->modify('-1 month');

        return $this->createQueryBuilder('r')
            ->where('r.updatedAt IS NOT NULL')
            ->andWhere('r.updatedAt < :date')
            ->andWhere('r.isRewarded = 1')
            ->setParameters([
                'date' => $date,
            ])
            ->getQuery()
            ->getResult();
    }

    public function findPublishersCountByParams($params)
    {
        $query = $this->createQueryBuilder('r')
            ->select(['r.id']);

        $query = $this->joinRelations($query);
        $query = $this->addParams($query, $params);

        return count($query->groupBy('pu.id')
            ->getQuery()
            ->getArrayResult());
    }

    public function findStatsByParams($params)
    {
        $query =  $this->createQueryBuilder('r')
            ->select(['pl.domain', 'pl.id as platformId', 'pu.name', 'SUM(r.offerPayout) as payout', 'COUNT(r.id) as rewardsCount, pu.apiToken']);

        $query = $this->joinRelations($query);
        $query = $this->addParams($query, $params);

        return $query->groupBy('pu.id')
            ->getQuery()
            ->getArrayResult();
    }

    public function findPlatformTotalRewardByParams($id, $params)
    {
        $query = $this->createQueryBuilder('r')
            ->select(['COUNT(r.id)']);

        $query = $this->joinRelations($query);
        $query = $this->addParams($query, $params);

        if ($id) {
            $query->groupBy('pl.id')
                ->andWhere('pl.id = :id')
                ->setParameter('id', $id);
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    public function findPlatformTotalPayoutByParams(int $id, $params)
    {
        $query = $this->createQueryBuilder('r')
            ->select(['SUM(r.offerPayout) as payout']);

        $query = $this->joinRelations($query);
        $query = $this->addParams($query, $params);

        if ($id) {
            $query->groupBy('pl.id')
                ->andWhere('pl.id = :id')
                ->setParameter('id', $id);
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    private function joinRelations(QueryBuilder $queryBuilder) : QueryBuilder
    {
        $queryBuilder
            ->join('r.application', 'a')
            ->join('a.publisher', 'pu')
            ->join('pu.platform', 'pl');

        return $queryBuilder;
    }

    private function addParams(QueryBuilder $queryBuilder, $params) : QueryBuilder
    {
        return $queryBuilder
            ->andwhere('r.createdAt >= :fromDate')
            ->andWhere('r.createdAt <= :toDate')
            ->setParameters($params);
    }
}