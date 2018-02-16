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

class PlatformController extends FOSRestController
{
    /**
     * A resource
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns platforms set",
     *  views = { "default", "skype" },
     *  statusCodes={
     *      200="Returned when successful",
     *      403="Returned when api token is missing or incorrect",
     *      404="Returned when something is not found",
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
    public function getPlatformsAction(Request $request)
    {
        $platforms = $this->get('cache')->get($request);

        if (!$platforms) {
            $platforms = $this
                ->getDoctrine()
                ->getRepository("AppBundle:Platform")
                ->findBySkypeId($request->headers->get("SKYPE-ID"));

            $this->get('cache')->set($request, $platforms);
        }

        return $platforms;
    }
}
