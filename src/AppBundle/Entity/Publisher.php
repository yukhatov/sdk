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
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\GeneratedValue;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping\Index;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PublisherRepository")
 * @ORM\Table(name="sdk_publisher", indexes={@Index(name="search_skype_id_idx", columns={"skype_id"})})
 * @UniqueEntity("apiToken")
 */
class Publisher extends BaseUser implements DatedInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @GeneratedValue
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Platform", inversedBy="publishers")
     * @ORM\JoinColumn(name="platform_id", onDelete="SET NULL")
     */
    private $platform;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, name="api_token", unique=true)
     */
    private $apiToken;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, name="name")
     */
    private $name;

    /**
     * @OneToMany(targetEntity="Application", mappedBy="publisher")
     */
    private $apps;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="skype_id")
     */
    private $skypeId;

    /**
     * @ORM\Column(type="boolean", nullable=false, name="is_skype_bot_active", options={"default" : 1})
     */
    private $isSkypeBotActive;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="created_at")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="updated_at")
     */
    private $updatedAt;

    /**
     * Publisher constructor.
     * @param $encoder
     * @param $id
     */
    public function __construct($encoder, $id)
    {
        $this->username = 'user' . $id;
        $this->password = $encoder->encodePassword($this, $this->username);
        $this->usernameCanonical = $this->username;
        $this->email = $this->username . '@gmail.com';
        $this->emailCanonical = $this->email;
        $this->enabled = 1;
    }

    /**
     * @return mixed
     */
    public function getIsSkypeBotActive()
    {
        return $this->isSkypeBotActive;
    }

    /**
     * @param mixed $isSkypeBotActive
     */
    public function setIsSkypeBotActive($isSkypeBotActive)
    {
        $this->isSkypeBotActive = $isSkypeBotActive;
    }

    /**
     * @return mixed
     */
    public function getSkypeId()
    {
        return $this->skypeId;
    }

    /**
     * @param mixed $skypeId
     */
    public function setSkypeId($skypeId)
    {
        $this->skypeId = $skypeId;
    }

    /**
     * @return mixed
     */
    public function getPlatform() :Platform
    {
        return $this->platform;
    }

    /**
     * @param mixed $platform
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;
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
    public function getUrlFetchById()
    {
        return 'http://' . $this->getPlatform()->getDomain() . Platform::URL_FETCH_BY_ID;
    }

    /**
     * @return mixed
     */
    public function getUrlFetch()
    {
        return 'http://' . $this->getPlatform()->getDomain() . Platform::URL_FETCH;
    }

    /**
     * @return mixed
     */
    public function getUrlFetchForSkype()
    {
        return 'http://' . $this->getPlatform()->getDomain() . Platform::URL_FETCH_SKYPE;
    }

    /**
     * @return string
     */
    public function getUrlFetchByIdForSkype()
    {
        return 'http://' . $this->getPlatform()->getDomain() . Platform::URL_FETCH_SKYPE_BY_ID;
    }

    /**
     * @return mixed
     */
    public function getUrlFetchBanners()
    {
        return 'http://' . $this->getPlatform()->getDomain() . Platform::URL_FETCH_BANNERS;
    }

    /**
     * @return mixed
     */
    public function getUrlOfferRequest()
    {
        return 'http://' . $this->getPlatform()->getDomain() . Platform::URL_OFFER_REQUEST;
    }

    /**
     * @return mixed
     */
    public function getUrlPostRadwallInstall()
    {
        return 'http://' . $this->getPlatform()->getDomain() . Platform::URL_POST_RADWALL_INSTALL;
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

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getApps()
    {
        return $this->apps;
    }

    /**
     * @param mixed $apps
     */
    public function setApps($apps)
    {
        $this->apps = $apps;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}