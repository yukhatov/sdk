<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 22.05.17
 * Time: 17:48
 */

namespace PartnerApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class PostbackController extends FOSRestController
{
    /**
     * @ApiDoc(
     *  resource = true,
     *  description="Creates new reward",
     *  views = { "default", "partner" },
     *  requirements={
     *      {
     *          "name"="click_id",
     *          "description"="click_id",
     *      }
     *  },
     *  statusCodes={
     *      201 = "Returned when created",
     *      400 = "Returned when the form has errors",
     *      403 = "Returned when api token is missing or incorrect",
     *  },
     *  headers={
     *      {
     *          "name"="API-AUTH-TOKEN",
     *          "description"="Authorization key",
     *          "required"="true"
     *      }
     *  }
     * )
     */
    public function getPostbackAction(string $click_id)
    {
        try {
            $reward = $this->get('transaction.id.generator')->decodeToReward($click_id);
        } catch (\Exception $e) {
            return new JsonResponse(['Data is not valid'], 400);
        }

        if (count($this->get('validator')->validate($reward))) {
            return new JsonResponse(['Reward is not valid or already posted'], 400);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($reward);
        $em->flush();

        return new JsonResponse(['id' => $reward->getId()], 201);
    }
}