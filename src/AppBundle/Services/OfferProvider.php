<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 21.02.17
 * Time: 12:24
 */
namespace AppBundle\Services;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Debug\Exception\ContextErrorException;

/**
 * Class OfferProvider
 * @package AppBundle\Services
 */
class OfferProvider
{
    /**
     * @param $url
     * @return array
     */
    public function fetchContent($url) : array
    {
        $context = stream_context_create(array(
            'http' => array(
                'ignore_errors' => true
            )
        ));

        try {
            $content = file_get_contents($url, false, $context);
        } catch (ContextErrorException $e) {
            throw new Exception("Name or service not known. (Publisher fetch url broken)");
        }

        $content = json_decode($content, true);

        if (!isset($content['success']) or !$content['success']) {
            throw new Exception($content['error_messages'][0] ?? "Service found. Bad publisher fetch url");
        }

        return [
            'totalCount' => intval($content['totalCount'] ?? 0),
            'offers'     => $content['offers'] ?? [],
        ];
    }

    /**
     * @param $url
     */
    public function plainRequest($url) : void
    {
        $context = stream_context_create(array(
            'http' => array(
                'ignore_errors' => true
            )
        ));

        try {
            file_get_contents($url, false, $context);
        } catch (ContextErrorException $e) {}
    }

    public function plainRequestWithResponse($url) : array
    {
        $context = stream_context_create(array(
            'http' => array(
                'ignore_errors' => true
            )
        ));

        try {
            $content = file_get_contents($url, false, $context);
        } catch (ContextErrorException $e) {
            throw new Exception("Name or service not known. (Publisher's url broken)");
        }

        $responseCode = substr($http_response_header[0], 9, 3);
        $content = json_decode($content, true);

        if (!isset($content['success']) or !$content['success']) {
            throw new Exception($content['error_messages'][0] ?? "Service found. Bad publisher fetch url");
        }

        return [
            'code' => $responseCode,
            'body' => $content['result'] ?? []
        ];
    }
}