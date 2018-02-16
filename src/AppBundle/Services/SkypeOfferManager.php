<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 21.02.17
 * Time: 12:24
 */
namespace AppBundle\Services;

use AppBundle\Entity\Publisher;
use AppBundle\Entity\PublisherStatistic;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Validator\Validator\RecursiveValidator;

class SkypeOfferManager
{
    /**
     * @var OfferProvider
     */
    private $offerProvider;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * @var Publisher
     */
    private $publisher;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RecursiveValidator
     */
    private $validator;

    /**
     * OfferProvider constructor.
     * @param OfferProvider $offerProvider
     * @param UrlGenerator $urlGenerator
     * @param TokenStorage $tokenStorage
     */
    public function __construct(OfferProvider $offerProvider, UrlGenerator $urlGenerator, TokenStorage $tokenStorage, EntityManager $em, RecursiveValidator $validator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->offerProvider = $offerProvider;
        $this->publisher = $tokenStorage->getToken()->getUser();
        $this->validator = $validator;
        $this->em = $em;
    }

    /**
     * Writes statistic
     */
    private function writeStatistic(string $type) : void {
        $publisherStatistic = $this->em
            ->getRepository("AppBundle:PublisherStatistic")
            ->findOneBy(['publisher' => $this->publisher->getId()]);

        if (!$publisherStatistic) {
            $publisherStatistic = new PublisherStatistic($this->publisher);
        }

        switch ($type) {
            case "REQUEST":
                $publisherStatistic->setOfferRequestsCount($publisherStatistic->getOfferRequestsCount() + 1);
                break;

            case "APPROVE":
                $publisherStatistic->setApproveRequestsCount($publisherStatistic->getApproveRequestsCount() + 1);
                break;
        }

        if (!count($this->validator->validate($publisherStatistic))) {
            $this->em->persist($publisherStatistic);
            $this->em->flush();
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function fetchById($id)
    {
        $url = $this->urlGenerator->generateUrlByIdForSkype($this->publisher, $id);
        $offer = $this->offerProvider->fetchContent($url)['offers'];

        if ($offer) {
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
        $url = $this->urlGenerator->generateUrlByParamsForSkype($this->publisher, $params);
        $content = $this->offerProvider->fetchContent($url);
        $this->writeStatistic("REQUEST");

        return [
            'totalCount' => $content['totalCount'],
            'count'      => count($content['offers']),
            'offers'     => $content['offers']
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function offerRequest($id) : array
    {
        $url = $this->urlGenerator->generateUrlOfferRequest($this->publisher, $id);
        $response = $this->offerProvider->plainRequestWithResponse($url);

        if($response['code'] != 400) {
            $this->writeStatistic("APPROVE");
        }

        return $response;
    }
}