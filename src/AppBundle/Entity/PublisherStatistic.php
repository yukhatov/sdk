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
use Doctrine\ORM\Mapping\Index;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PublisherStatisticRepository")
 * @ORM\Table(name="sdk_publisher_statistic", indexes={@Index(name="search__publisher_id_idx", columns={"publisher_id"})})
 * @UniqueEntity("publisher")
 */
class PublisherStatistic
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @GeneratedValue
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Publisher")
     * @ORM\JoinColumn(name="publisher_id", onDelete="SET NULL", unique=true)
     * @Assert\NotNull()
     */
    private $publisher;

    /**
     * @ORM\Column(type="integer", nullable=false, name="offer_requests_count")
     * @Assert\NotBlank()
     */
    private $offerRequestsCount;

    /**
     * @ORM\Column(type="integer", nullable=false, name="approve_requests_count")
     * @Assert\NotBlank()
     */
    private $approveRequestsCount;

    /**
     * PublisherStatistic constructor.
     * @param $publisher
     */
    public function __construct($publisher)
    {
        $this->publisher = $publisher;
        $this->approveRequestsCount = 0;
        $this->offerRequestsCount = 0;
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
    public function getOfferRequestsCount()
    {
        return $this->offerRequestsCount;
    }

    /**
     * @param mixed $offerRequestsCount
     */
    public function setOfferRequestsCount($offerRequestsCount)
    {
        $this->offerRequestsCount = $offerRequestsCount;
    }

    /**
     * @return mixed
     */
    public function getApproveRequestsCount()
    {
        return $this->approveRequestsCount;
    }

    /**
     * @param mixed $approveRequestsCount
     */
    public function setApproveRequestsCount($approveRequestsCount)
    {
        $this->approveRequestsCount = $approveRequestsCount;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

}