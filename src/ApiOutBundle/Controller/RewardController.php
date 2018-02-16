<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 19.05.17
 * Time: 10:01
 */

namespace ApiOutBundle\Controller;

use AppBundle\Entity\Reward;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\RewardType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\NoResultException;

class RewardController extends FOSRestController
{
    /**
     * A resource
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns rewards",
     *  views = { "default", "sdk" },
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
     * @QueryParam(name="isRewarded", nullable=true, requirements="[01]", strict=true, description="isRewarded filter. By default 0 and 1")
     */
    public function getRewardsAction(ParamFetcher $paramFetcher, Request $request)
    {
        $rewards = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Reward')
            ->findByParams(array_merge($paramFetcher->all(), $request->headers->all()));

        return $rewards;
    }

    /**
     * @ApiDoc(
     *  resource = true,
     *  description="Sets reward's isRawarded property to true",
     *  views = { "default", "sdk" },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="reward id",
     *      }
     *  },
     *  statusCodes={
     *      200 = "Returned when updated",
     *      400 = "Returned when the form has errors",
     *      403 = "Returned when api token is missing or incorrect",
     *      404 = "Returned when reward not found",
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
     */
    public function patchRewardAction(Request $request, $id)
    {
        if (!is_numeric($id)) return new JsonResponse('Offer Id must be numeric', 400);

        $em = $this->getDoctrine()->getManager();

        try {
            $reward = $em->getRepository('AppBundle:Reward')
                ->findOneByParamsAndId($request->headers->all(), $id);
        } catch (NoResultException $e) {
            return new JsonResponse('Not Found', 404);
        }

        if ($reward->getIsRewarded()) {
            return new JsonResponse('OK', 200);
        }

        $reward->setIsRewarded(1);

        if (count($this->get('validator')->validate($reward))) {
            return new JsonResponse('Bad request', 400);
        }

        $em->persist($reward);
        $em->flush();

        return new JsonResponse('OK', 200);
    }
}
