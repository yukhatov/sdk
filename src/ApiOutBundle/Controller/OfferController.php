<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 23.02.17
 * Time: 18:56
 */

namespace ApiOutBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use AppBundle\Entity\Offer;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\View\View;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class OfferController extends FOSRestController
{
    /**
     * A resource
     * @ApiDoc(
     *  resource=true,
     *  description="Returns single offer by id",
     *  views = { "default", "sdk" },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="offer id",
     *      }
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      403="Returned when api token is missing or incorrect",
     *      404={
     *        "Returned when offer is not found",
     *        "Returned when something else is not found"
     *      },
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
     */
    public function getOfferAction(Request $request, $id)
    {
        if (!is_numeric($id)) return $this->generateView(400, "Offer Id must be numeric");

        try {
            $offer = $this->get('cache')->get($request);

            if (!$offer) {
                $offer = $this->get('sdk.offer.manager')->fetchById($id);

                $this->get('cache')->set($request, $offer);
            }
        } catch (Exception $e) {
            return $this->generateView(424, $e->getMessage());
        }

        if (!$offer) {
            return $this->generateView(404);
        }

        return new JsonResponse($offer);
    }

    /**
     * A resource
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns offers set",
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
     * @QueryParam(name="offset", nullable=true, requirements="\d+", strict=true, description="Index number of first element for return")
     * @QueryParam(name="limit", nullable=true, requirements="\d+", strict=true, description="Total numbers for return", default="999")
     * @QueryParam(name="countryCode", nullable=true, requirements="[A-Z,]+", strict=true, description="Country code. Accepts multiple codes(US,UA)")
     * @QueryParam(name="platform", nullable=true, requirements="[a-zA-Z,]+", strict=true, description="Platform name (iPhone, iPad, Android). Accepts multiple platforms(iPad,Android)")
     */
    public function getOffersAction(Request $request, ParamFetcher $paramFetcher)
    {
        try {
            $offers = $this->get('cache')->get($request);

            if (!$offers) {
                $offers = $this->get('sdk.offer.manager')->fetchByParams($paramFetcher->all());

                $this->get('cache')->set($request, $offers);
            }
        } catch (Exception $e) {
            return $this->generateView(424, $e->getMessage());
        }

        $response = new JsonResponse($offers);
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