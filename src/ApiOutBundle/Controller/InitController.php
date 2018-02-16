<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 31.05.17
 * Time: 12:22
 */

namespace ApiOutBundle\Controller;

use AppBundle\Entity\Init;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\JsonResponse;

class InitController extends FOSRestController
{
    /**
     * A resource
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns init data",
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
     */
    public function getInitAction(Request $request)
    {
        $application = $request->request->get('application');

        if (!$application) {
            return new JsonResponse('Not Found', 404);
        }

        return [
            'store_app_id' =>  $application->getStoreAppId(),
            'virtual_currency' =>  $application->getVirtualCurrency(),
            'is_sand_box' =>  $application->getIsSandBox(),
            'is_rewarded' =>  $application->getIsRewarded(),
            'is_quick_reward' =>  $application->getIsQuickReward(),
            'exchange_rate' =>  $application->getExchangeRate(),
        ];
    }
}
