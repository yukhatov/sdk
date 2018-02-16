<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 21.02.17
 * Time: 10:27
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Core\DatedInterface;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class PlatformApiResponse
{
    private $domain;

    private $apiToken;

    /**
     * PlatformApiResponse constructor.
     * @param $domain
     * @param $apiToken
     */
    public function __construct($domain, $apiToken)
    {
        $this->domain = $domain;
        $this->apiToken = $apiToken;
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return mixed
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }
}