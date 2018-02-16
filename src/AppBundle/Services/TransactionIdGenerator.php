<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 17.05.17
 * Time: 15:57
 */

namespace AppBundle\Services;

use AppBundle\Entity\Application;
use AppBundle\Entity\Reward;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;

/**
 * Class TransactionIdGenerator
 * @package AppBundle\Services
 */
class TransactionIdGenerator
{
    const APPLICATON_ID_KEY = 'a';
    const DEVICE_ID_KEY = 'd';
    const OFFER_ID_KEY = 'o';
    const OFFER_PAYOUT_KEY = 'p';
    const REWARD_AMOUNT_KEY = 'r';

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * TransactionIdGenerator constructor.
     * @param RequestStack $requestStack
     * @param UrlGenerator $urlGenerator
     * @param EntityManager $em
     */
    public function __construct(RequestStack $requestStack, UrlGenerator $urlGenerator, EntityManager $em)
    {
        $this->requestStack = $requestStack;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $em;
    }

    /**
     * @param Application $application
     * @param int $offerId
     * @param int $rewardAmount
     * @return string
     */
    public function generate(Application $application, $deviceId, int $offerId, int $rewardAmount, $offerPayout) : string
    {
        $trackingUrlData = [
            self::APPLICATON_ID_KEY => $application->getId(),
            self::DEVICE_ID_KEY => $deviceId,
            self::OFFER_ID_KEY => $offerId,
            self::OFFER_PAYOUT_KEY => $offerPayout,
            self::REWARD_AMOUNT_KEY => $rewardAmount,
        ];

        return urlencode(base64_encode(serialize($trackingUrlData)));
    }

    /**
     * @param string $transactionId
     * @return Reward
     * @throws \Exception
     */
    public function decodeToReward(string $transactionId) : Reward
    {
        $data = unserialize(base64_decode(urldecode($transactionId)));

        $application = $this->entityManager
            ->getRepository("AppBundle:Application")
            ->find($data[self::APPLICATON_ID_KEY]);

        if (!$application or !$application->getPublisher()) {
            throw new \Exception();
        }

        $isRewarded = false;

        if (!$application->getIsRewarded() or ($application->getIsRewarded() and $application->getIsQuickReward())) {
            $isRewarded = true;
        }

        $reward = new Reward();
        $reward->setApplication($application);
        $reward->setDeviceId($data[self::DEVICE_ID_KEY] ?? '');
        $reward->setOfferId($data[self::OFFER_ID_KEY] ?? '');
        $reward->setOfferPayout($data[self::OFFER_PAYOUT_KEY] ?? '');
        $reward->setAmount($data[self::REWARD_AMOUNT_KEY] ?? '');
        $reward->setIsRewarded($isRewarded);

        return $reward;
    }

    /**
     * Get actual offer payout from platfrom
     */
    private function getOfferPayout(int $id) : float {
        if (!$id) {
            return 0;
        }

        $url = $this->urlGenerator->generateUrlById($this->application, $id);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        $content = json_decode($response, true);

        return $content['offers']['Payout'] ?? 0;
    }
}