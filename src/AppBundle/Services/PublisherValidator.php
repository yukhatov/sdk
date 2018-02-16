<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 24.04.17
 * Time: 15:49
 */

namespace AppBundle\Services;

use AppBundle\Entity\Publisher;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\Validator\Validator\RecursiveValidator;

/**
 * Class UrlGenerator
 * @package AppBundle\Services
 */
class PublisherValidator
{
    const HTTP_RESPONSE_CODE_HEADER_INDEX = 0;
    const HTTP_CODE_START_POS = 9;
    const HTTP_CODE_LENGTH = 3;

    /**
     * @var RecursiveValidator
     */
    private $validator;

    /**
     * PublisherValidator constructor.
     * @param RecursiveValidator $validator
     */
    public function __construct(RecursiveValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param Publisher $publisher
     * @return bool
     */
    public function validate(Publisher $publisher)
    {
        if (
            count($this->validator->validate($publisher)) or
            !($this->isValidUrl(str_replace('{apiKey}', $publisher->getApiToken(), $publisher->getUrlFetch()))) or
            !($this->isValidUrl(str_replace('{apiKey}', $publisher->getApiToken(), $publisher->getUrlFetchById())))
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param $url
     * @return bool
     */
    private function isValidUrl($url)
    {
        switch ($this->getHttpResponseCode($url)) {
            case 0:   // bad url
            case 401: // invalid token
            case 404: // bad url
                return false;
            default:
                return true;
        }
    }

    /**
     * @param $url
     * @return int|string
     */
    private function getHttpResponseCode($url)
    {
        try {
            $headers = get_headers($url);
        } catch (ContextErrorException $e) {
            return 0;
        }

        return substr($headers[self::HTTP_RESPONSE_CODE_HEADER_INDEX], self::HTTP_CODE_START_POS, self::HTTP_CODE_LENGTH);
    }
}