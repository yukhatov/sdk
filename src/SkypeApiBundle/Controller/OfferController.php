<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 23.02.17
 * Time: 18:56
 */

namespace SkypeApiBundle\Controller;

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
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="string",
     *          "requirement"="[0-9,]+",
     *          "description"="Offer id. Accepts multiple values (1,2,3)",
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
     *          "name"="SKYPE-ID",
     *          "description"="Application identificator",
     *          "required"="true"
     *      }
     *  }
     * )
     */
    public function getOfferAction(Request $request, $id)
    {
        try {
            $offer = $this->get('cache')->get($request);

            if (!$offer) {
                $offer = $this->get('skype.offer.manager')->fetchById($id);

                $this->get('cache')->set($request, $offer);
            }
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 424);
        }

        if (!$offer) {
            return new JsonResponse(['message' => "Not Found"], 404);
        }

        return new JsonResponse($offer);
    }

    /**
     * A resource
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns offers set",
     *  views = { "default", "skype" },
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
     *          "name"="SKYPE-ID",
     *          "description"="Skype account identificator",
     *          "required"="true"
     *      }
     *  }
     * )
     *
     * @QueryParam(name="offset", nullable=true, requirements="\d+", strict=true, description="Index number of first element for return")
     * @QueryParam(name="limit", nullable=true, requirements="\d+", strict=true, description="Total numbers for return", default="5")
     * @QueryParam(name="countryCode", nullable=true, requirements="[A-Z,]+", strict=true, description="Country code. Accepts multiple codes(US,UA)")
     * @QueryParam(name="platform", nullable=true, requirements="[a-zA-Z,]+", strict=true, description="Platform name (iPhone, iPad, Android). Accepts multiple platforms(iPad,Android)")
     * @QueryParam(name="incent", nullable=true, requirements="[01]", strict=true, description="Incent. By default 0 and 1")
     * @QueryParam(name="minPayout", nullable=true, requirements="^[0-9]*[.]?[0-9]+$", strict=true, description="Min payout value")
     * @QueryParam(name="maxPayout", nullable=true, requirements="^[0-9]*[.]?[0-9]+$", strict=true, description="Max payout value")
     * @QueryParam(name="halfCapsLeft", nullable=true, requirements="[01]", strict=true, description="More than 50% CAP left")
     * @QueryParam(name="time", nullable=true, requirements="\d+", strict=true, description="Created not later than specified hours")
     * @QueryParam(name="offerType", nullable=true, requirements="[123]", strict=true, description="3-CPA; 2-Mobile and Dynamic; 1-Both;")
     */
    public function getOffersAction(Request $request, ParamFetcher $paramFetcher)
    {
        try {
            $offers = $this->get('cache')->get($request);

            if (!$offers) {
                $offers = $this->get('skype.offer.manager')->fetchByParams($paramFetcher->all());

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
     * @ApiDoc(
     *  resource = true,
     *  description="Requests offer for publisher",
     *  views = { "default", "skype" },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="string",
     *          "requirement"="[0-9,]+",
     *          "description"="Оffer id. Accepts multiple ids(1,2,3)",
     *      }
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when offer already requested",
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
     *          "name"="SKYPE-ID",
     *          "description"="Skype account identificator",
     *          "required"="true"
     *      }
     *  }
     * )
     */
    public function getOffersRequestAction($id)
    {
        // Внимание, костыль
        if (!preg_match('/^[0-9,]+$/', $id)) return $this->generateView(400, "Offer Id must match [0-9,]+");

        try {
            $reponse = $this->get('skype.offer.manager')->offerRequest($id);
        } catch (Exception $e) {
            return $this->generateView(424, $e->getMessage());
        }

        return $this->generateView($reponse['code'], $reponse['body']);
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
