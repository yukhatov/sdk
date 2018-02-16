<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 19.04.17
 * Time: 12:40
 */

namespace AppBundle\Services;

use AppBundle\Entity\Application;
use AppBundle\Entity\Publisher;

/**
 * Class UrlGenerator
 * @package AppBundle\Services
 */
class UrlGenerator
{
    /**
     * @param Application $application
     * @param $id
     * @return mixed
     */
    public function generateUrlByIdSandBox(Application $application, $id)
    {
        $url = str_replace(['{apiKey}', '{offerId}', '{appId}', '/offers/'], [$application->getPublisher()->getApiToken(), $id, $application->getStoreAppId(), '/sandbox/'], $application->getPublisher()->getUrlFetchById());

        return $url;
    }

    /**
     * @param Application $application
     * @param $id
     * @return mixed
     */
    public function generateUrlById(Application $application, $id)
    {
        $url = str_replace(['{apiKey}', '{offerId}', '{appId}'], [$application->getPublisher()->getApiToken(), $id, $application->getStoreAppId()], $application->getPublisher()->getUrlFetchById());

        return $url;
    }

    /**
     * @param Application $application
     * @param $params
     * @return mixed|string
     */
    public function generateUrlByParamsSandBox(Application $application, $params)
    {
        $url = str_replace(['{apiKey}', '{appId}', '/offers/'], [$application->getPublisher()->getApiToken(), $application->getStoreAppId(), '/sandbox/'], $application->getPublisher()->getUrlFetch());

        //  ignore country filter
        unset($params['countryCode']);

        foreach ($params as $paramCode => $param) {
            $url = $url . "&" . $paramCode . "=" . $param;
        }

        return $url;
    }

    /**
     * @param Application $application
     * @param $params
     * @return mixed|string
     */
    public function generateUrlByParams(Application $application, $params)
    {
        $url = str_replace(['{apiKey}', '{appId}'], [$application->getPublisher()->getApiToken(), $application->getStoreAppId()], $application->getPublisher()->getUrlFetch());

        foreach ($params as $paramCode => $param) {
            $url = $url . "&" . $paramCode . "=" . $param;
        }

        return $url;
    }

    /**
     * @param Application $application
     * @param $params
     * @return mixed|string
     */
    public function generateUrlBanners(Application $application, $params)
    {
        $url = str_replace(['{apiKey}', '{appId}'], [$application->getPublisher()->getApiToken(), $application->getStoreAppId()], $application->getPublisher()->getUrlFetchBanners());

        foreach ($params as $paramCode => $param) {
            $url = $url . "&" . $paramCode . "=" . $param;
        }

        return $url;
    }

    /**
     * @param Application $application
     * @param $params
     * @return mixed|string
     */
    public function generateUrlBannersSandBox(Application $application, $params)
    {
        $url = str_replace(['{apiKey}', '{appId}', '/offers/'], [$application->getPublisher()->getApiToken(), $application->getStoreAppId(), '/sandbox/'], $application->getPublisher()->getUrlFetchBanners());

        foreach ($params as $paramCode => $param) {
            $url = $url . "&" . $paramCode . "=" . $param;
        }

        return $url;
    }


    /**
     * @param array $params
     * @param Publisher $publisher
     * @return mixed|string
     */
    public function generateUrlByParamsForSkype(Publisher $publisher, array $params)
    {
        $url = str_replace(['{apiKey}'], [$publisher->getApiToken()], $publisher->getUrlFetchForSkype());

        foreach ($params as $paramCode => $param) {
            $url = $url . "&" . $paramCode . "=" . $param;
        }

        return $url;
    }

    /**
     * @param Publisher $publisher
     * @param $id
     * @return mixed
     */
    public function generateUrlByIdForSkype(Publisher $publisher, $id)
    {
        $url = str_replace(['{apiKey}', '{offerId}'], [$publisher->getApiToken(), $id], $publisher->getUrlFetchByIdForSkype());

        return $url;
    }

    /**
     * @param int $id
     * @param Publisher $publisher
     * @return string
     */
    public function generateUrlOfferRequest(Publisher $publisher, string $id) : string
    {
        $url = str_replace(['{apiKey}', '{offerId}'], [$publisher->getApiToken(), $id], $publisher->getUrlOfferRequest());

        return $url;
    }

    /**
     * @param Publisher $publisher
     * @return string
     */
    public function generateUrlPostReadwallInstall(Publisher $publisher) : string
    {
        $url = str_replace(['{apiKey}', '{outletId}'], [$publisher->getApiToken(), $publisher->getId()], $publisher->getUrlPostRadwallInstall());

        return $url;
    }
}