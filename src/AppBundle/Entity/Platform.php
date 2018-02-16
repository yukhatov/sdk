<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 19.05.17
 * Time: 13:14
 */

namespace AppBundle\Entity;

use AppBundle\Core\Dated;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Core\DatedInterface;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlatformRepository")
 * @ORM\Table(name="sdk_platform")
 */
class Platform implements DatedInterface
{
    const URL_FETCH = '/sdk/offers/findAll?token={apiKey}&appId={appId}';
    const URL_FETCH_BY_ID  = '/sdk/offers/findById?token={apiKey}&appId={appId}&offerId={offerId}';
    const URL_FETCH_BANNERS  = '/sdk/offers/banners?token={apiKey}&appId={appId}';
    const URL_FETCH_SKYPE  = '/chat-bots/offers/findAll?token={apiKey}';
    const URL_FETCH_SKYPE_BY_ID  = '/chat-bots/offers/findById?token={apiKey}&offerId={offerId}';
    const URL_OFFER_REQUEST  = '/chat-bots/offers/approvalRequest?token={apiKey}&offersIds={offerId}';
    const URL_POST_RADWALL_INSTALL  = '/sdk/apps/regular-app/addInstall?token={apiKey}&outletId={outletId}';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false, name="domain")
     */
    private $domain;

    /**
     * @ORM\OneToMany(targetEntity="Publisher", mappedBy="platform")
     */
    private $publishers;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="created_at")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="updated_at")
     */
    private $updatedAt;

    /**
     * Platform constructor.
     */
    public function __construct()
    {
        $this->publishers = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param mixed $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getPublishers()
    {
        return $this->publishers;
    }

    /**
     * @param mixed $publishers
     */
    public function setPublishers($publishers)
    {
        $this->publishers = $publishers;
    }
}