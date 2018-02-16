<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 19.05.17
 * Time: 13:14
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Core\DatedInterface;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotNullValidator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RewardRepository")
 * @ORM\Table(name="sdk_reward",
 *     uniqueConstraints={@UniqueConstraint(name="composite", columns={"application_id", "device_id", "offer_id"})})
 * @UniqueEntity(fields={"application", "deviceId", "offerId"})
 */
class Reward implements DatedInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @GeneratedValue
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Application")
     * @ORM\JoinColumn(name="application_id", onDelete="SET NULL")
     * @Assert\NotNull()
     */
    private $application;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, name="device_id")
     * @Assert\NotBlank()
     */
    private $deviceId;

    /**
     * @ORM\Column(type="integer", nullable=false, name="offer_id")
     * @Assert\NotBlank()
     */
    private $offerId;

    /**
     * @ORM\Column(type="float", nullable=false, name="offer_payout", options={"default" : 0})
     */
    private $offerPayout;

    /**
     * @ORM\Column(type="integer", nullable=false, name="amount")
     * @Assert\NotBlank()
     */
    private $amount;

    /**
     * @ORM\Column(type="boolean", nullable=false, name="is_rewarded", options={"default" : 0})
     */
    private $isRewarded;

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
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param mixed $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * @return mixed
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * @param mixed $deviceId
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;
    }

    /**
     * @return mixed
     */
    public function getOfferPayout()
    {
        return $this->offerPayout;
    }

    /**
     * @param mixed $offerPayout
     */
    public function setOfferPayout($offerPayout)
    {
        $this->offerPayout = $offerPayout;
    }

    /**
     * @return mixed
     */
    public function getOfferId()
    {
        return $this->offerId;
    }

    /**
     * @param mixed $offerId
     */
    public function setOfferId($offerId)
    {
        $this->offerId = $offerId;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
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
}