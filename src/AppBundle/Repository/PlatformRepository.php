<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 21.03.17
 * Time: 11:58
 */
namespace AppBundle\Repository;

use AppBundle\Entity\PlatformApiResponse;
use Doctrine\ORM\EntityRepository;

/**
 * Class PlatformRepository
 * @package AppBundle\Repository
 */
class PlatformRepository extends EntityRepository
{
    /**
     * @param string $skypeId
     * @return array
     */
    public function findBySkypeId(string $skypeId) : array
    {
        $platforms = $this->createQueryBuilder('pl')
            ->addSelect('pu.apiToken')
            ->join("pl.publishers", "pu")
            ->where('pu.skypeId = :skypeId')
            ->setParameters([
                'skypeId' => $skypeId,
            ])
            ->getQuery()
            ->getArrayResult();
        $result = [];

        /* TODO: костыльбанище исправить*/
        foreach ($platforms as $platform) {
            $result[] = new PlatformApiResponse(str_replace('api.', '', $platform[0]['domain']), $platform['apiToken']);
        }

        return $result;
    }
}