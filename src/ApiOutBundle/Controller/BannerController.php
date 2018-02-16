<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 23.02.17
 * Time: 18:56
 */

namespace ApiOutBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\View\View;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BannerController extends FOSRestController
{
    /**
     * A resource
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns banners set",
     *  views = { "default", "sdk" },
     *  statusCodes={
     *      200="Returned when successful",
     *      403="Returned when api token is missing or incorrect",
     *      404="Returned when something is not found",
     *      424="Returned when request to platform failed",
     *  },
     *  headers={
     *      {
     *          "name"="API-AUTH-TOKEN",
     *          "description"="Authorization key",
     *          "required"="true"
     *      },
     *     {
     *          "name"="APP-ID",
     *          "description"="Application identificator",
     *          "required"="true"
     *      },
     *     {
     *          "name"="DEVICE-ID",
     *          "description"="Device identificator",
     *          "required"="true"
     *      }
     *  }
     * )
     *
     * @QueryParam(name="countryCode", nullable=true, requirements="[A-Z,]+", strict=true, description="Country code. Accepts multiple codes(US,UA)")
     * @QueryParam(name="platform", nullable=true, requirements="[a-zA-Z,]+", strict=true, description="Platform name (iPhone, iPad, Android). Accepts multiple platforms(iPad,Android)")
     */
    public function getBannersAction(Request $request, ParamFetcher $paramFetcher)
    {
        try {
            $banners = $this->get('cache')->get($request);

            if (!$banners) {
                $banners = $this->get('sdk.offer.manager')->fetchBanners($paramFetcher->all());

                $this->get('cache')->set($request, $banners);
            }
        } catch (Exception $e) {
            return $this->generateView(424, $e->getMessage());
        }

        $response = new JsonResponse($banners);
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        
        return $response;
    }

    /**
     * @param $statusCode
     * @param null $message
     * @return static
     */
    private function generateView($statusCode, $message = null)
    {
        $view = View::create();
        $view->setStatusCode($statusCode);
        $view->setData($message);

        return $view;
    }
}