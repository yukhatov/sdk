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

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ApplicationRepository")
 * @ORM\Table(name="sdk_application",
 *          uniqueConstraints={@UniqueConstraint(name="composite", columns={"store_app_id", "publisher_id"})})
 * @UniqueEntity(fields={"storeAppId", "publisher"})
 */
class Application implements DatedInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, name="store_app_id")
     */
    private $storeAppId;

    /**
     * @ORM\Column(type="boolean", nullable=false, name="is_sand_box", options={"default" : 0})
     */
    private $isSandBox;

    /**
     * @ManyToOne(targetEntity="Publisher", inversedBy="apps")
     * @ORM\JoinColumn(name="publisher_id", onDelete="SET NULL")
     */
    private $publisher;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, name="virtual_currency")
     */
    private $virtualCurrency;

    /**
     * @ORM\Column(type="integer", nullable=false, name="exchange_rate", options={"default" : 100})
     */
    private $exchangeRate;

    /**
     * @ORM\Column(type="boolean", nullable=false, name="is_rewarded", options={"default" : 0})
     */
    private $isRewarded;

    /**
     * @ORM\Column(type="boolean", nullable=false, name="is_quick_reward", options={"default" : 0})
     */
    private $isQuickReward;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="created_at")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="updated_at")
     */
    private $updatedAt;

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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getStoreAppId()
    {
        return $this->storeAppId;
    }

    /**
     * @param mixed $storeAppId
     */
    public function setStoreAppId($storeAppId)
    {
        $this->storeAppId = $storeAppId;
    }

    /**
     * @return mixed
     */
    public function getIsSandBox()
    {
        return $this->isSandBox;
    }

    /**
     * @param mixed $isSandBox
     */
    public function setIsSandBox($isSandBox)
    {
        $this->isSandBox = $isSandBox;
    }

    /**
     * @return mixed
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @param mixed $publisher
     */
    public function setPublisher($publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * @return mixed
     */
    public function getVirtualCurrency()
    {
        return $this->virtualCurrency;
    }

    /**
     * @param mixed $virtualCurrency
     */
    public function setVirtualCurrency($virtualCurrency)
    {
        $this->virtualCurrency = $virtualCurrency;
    }

    /**
     * @return mixed
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * @param mixed $exchangeRate
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }

    /**
     * @return mixed
     */
    public function getIsRewarded()
    {
        return $this->isRewarded;
    }

    /**
     * @param mixed $isRewarded
     */
    public function setIsRewarded($isRewarded)
    {
        $this->isRewarded = $isRewarded;
    }

    /**
     * @return mixed
     */
    public function getIsQuickReward()
    {
        return $this->isQuickReward;
    }

    /**
     * @param mixed $isQuickReward
     */
    public function setIsQuickReward($isQuickReward)
    {
        $this->isQuickReward = $isQuickReward;
    }
}