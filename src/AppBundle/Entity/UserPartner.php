<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 21.02.17
 * Time: 10:27
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\GeneratedValue;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserPartnerRepository")
 * @ORM\Table(name="sdk_user_partner")
 */
class UserPartner extends BaseUser
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, name="api_token")
     */
    private $apiToken;

    public function __construct()
    {
        $this->apiToken = bin2hex(random_bytes(10));
    }

    /**
     * @return mixed
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * @param mixed $apiToken
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
    }
}