<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 21.02.17
 * Time: 12:24
 */
namespace AppBundle\Services;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Device;

/**
 * Class SDKOfferManager
 * @package AppBundle\Services
 */
class SDKOfferManager
{
    const RADWALL_APP_ID = "com.autobuild.app";

    /**
     * @var TransactionIdGenerator
     */
    private $transactionIdGenerator;

    /**
     * @var OfferProvider
     */
    private $offerProvider;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * @var Aplication
     */
    private $application;

    /**
     * @var array|string
     */
    private $deviceId;

    /**
     * @var RecursiveValidator
     */
    private $validator;

    /**
     * @var
     */
    private $em;

    /**
     * OfferProvider constructor.
     * @param RequestStack $requestStack
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(RequestStack $requestStack, UrlGenerator $urlGenerator, TransactionIdGenerator $transactionIdGenerator, OfferProvider $offerProvider, RecursiveValidator $validator, EntityManager $em)
    {
        $this->urlGenerator = $urlGenerator;
        $this->offerProvider = $offerProvider;
        $this->transactionIdGenerator = $transactionIdGenerator;
        $this->deviceId = $requestStack->getCurrentRequest()->headers->get('DEVICE-ID');
        $this->application = $requestStack->getCurrentRequest()->get('application');
        $this->validator = $validator;
        $this->em = $em;

        $this->writeStatistic();
    }

    /**
     * Writes statistic to platform and creates unique devices
     */
    private function writeStatistic() : void {
        /* Create new device if does not exist */
        $device = new Device($this->application, $this->deviceId);

        if (!count($this->validator->validate($device))) {
            $this->em->persist($device);
            $this->em->flush();

            if ($this->application->getStoreAppId() == self::RADWALL_APP_ID) {
                $this->offerProvider->plainRequest($this->urlGenerator->generateUrlPostReadwallInstall($this->application->getPublisher()));
            }
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function fetchById($id)
    {
        $url = $this->buildUrlById($id, $this->application->getIsSandBox());
        $offer = $this->offerProvider->fetchContent($url)['offers'];

        if ($offer) {
            if (!$this->application->getIsSandBox()) {
                $offer = $this->feedTrackingUrl([$offer]);
            }

            return $offer;
        }

        return false;
    }

    /**
     * @param $params
     * @return array
     */
    public function fetchByParams($params)
    {
        $url = $this->buildUrlByParams($params, $this->application->getIsSandBox());
        $content = $this->offerProvider->fetchContent($url);

        if (!$this->application->getIsSandBox()) {
            $content['offers'] = $this->feedTrackingUrl($content['offers']);
        }

        return [
            'totalCount' => $content['totalCount'],
            'count'      => count($content['offers']),
            'offers'     => $content['offers']
        ];
    }

    /**
     * @param $params
     * @return array
     */
    public function fetchBanners($params)
    {
        $url = $this->buildUrlBanners($params, $this->application->getIsSandBox());
        $content = $this->offerProvider->fetchContent($url);

        if (!$this->application->getIsSandBox()) {
            $content['offers'] = $this->feedTrackingUrl($content['offers']);
        }

        return [
            'totalCount' => $content['totalCount'],
            'count'      => count($content['offers']),
            'offers'     => $content['offers']
        ];
    }

    /**
     * @param $offers
     * @return mixed
     */
    private function feedTestData($offers)
    {
        foreach ($offers as $keyOffer => $offer) {
            if (isset($offer['Tracking_url'])) {
                $url = $offer['Tracking_url'] . '&aff_sub=testoffer';
                $offers[$keyOffer]['Tracking_url'] = $url;
            }
        }

        return $offers;
    }

    /**
     * @param array $offers
     * @return array
     */
    private function feedTrackingUrl(array $offers) : array
    {
        foreach ($offers as $keyOffer => $offer) {
            $url = $offer['Tracking_url'] . '&click_id=' . $this->transactionIdGenerator->generate(
                $this->application,
                $this->deviceId,
                $offer['ID'] ?? 0,
                $offer['Reward_amount'] ?? 0,
                $offer['Payout'] ?? 0
            );

            $url = $url . '&isSdk=1';
            $offers[$keyOffer]['Tracking_url'] = $url;
        }

        return $offers;
    }

    /**
     * @param $id
     * @param $isSandBox
     * @return mixed
     */
    private function buildUrlById($id, $isSandBox)
    {
        return $isSandBox ?
            $this->urlGenerator->generateUrlByIdSandBox($this->application, $id) :
            $this->urlGenerator->generateUrlById($this->application, $id);
    }

    /**
     * @param $params
     * @param $isSandBox
     * @return mixed|string
     */
    private function buildUrlByParams($params, $isSandBox)
    {
        return $isSandBox ?
            $this->urlGenerator->generateUrlByParamsSandBox($this->application, $params) :
            $this->urlGenerator->generateUrlByParams($this->application, $params);
    }

    /**
     * @param $params
     * @param $isSandBox
     * @return mixed|string
     */
    private function buildUrlBanners($params, $isSandBox)
    {
        return $isSandBox ?
            $this->urlGenerator->generateUrlBannersSandBox($this->application, $params) :
            $this->urlGenerator->generateUrlBanners($this->application, $params);
    }
}