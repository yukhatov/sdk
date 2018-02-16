<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 17.03.17
 * Time: 16:53
 */
namespace PartnerApiBundle\Controller;

use AppBundle\Entity\Publisher;
use AppBundle\Entity\Application;
use AppBundle\Form\ApplicationType;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class ApplicationController extends FOSRestController
{
    /**
     * A resource
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns single application by id",
     *  views = { "default", "partner" },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="application id",
     *      }
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      403="Returned when api token is missing or incorrect",
     *      404={
     *        "Returned when application is not found",
     *        "Returned when something else is not found"
     *      },
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
    public function getApplicationAction(Application $id)
    {
        return $id;
    }

    /**
     * A resource
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Removes application",
     *  views = { "default", "partner" },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="application id",
     *      }
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when not deleted",
     *      403="Returned when api token is missing or incorrect",
     *      404={
     *        "Returned when application is not found",
     *      },
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
    public function deleteApplicationAction(Application $id)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($id);
        $em->flush();

        if (!$em->contains($id)) {
            return new JsonResponse([], 200);
        }

        return new JsonResponse([], 400);
    }

    /**
     * @ApiDoc(
     *  resource = true,
     *  description="Posts user application",
     *  views = { "default", "partner" },
     *  statusCodes={
     *      201 = "Returned when created",
     *      400 = "Returned when the form has errors or application already exists",
     *      403 = "Returned when api token is missing or incorrect",
     *      404 = "Returned when publisher not found",
     *  },
     *  headers={
     *      {
     *          "name"="API-AUTH-TOKEN",
     *          "description"="Authorization key",
     *          "required"="true"
     *      }
     *  }
     * )
     *
     * @RequestParam(name="publisherId", requirements="\d+", nullable=false, strict=true, description="Publisher id")
     * @RequestParam(name="isSandBox", requirements="[01]", nullable=false, strict=true, description="Application on sandbox mode")
     * @RequestParam(name="storeAppId", requirements="^\w*(\.\w+)+\w$|\d{9,}", nullable=false, strict=true, description="Application store id")
     * @RequestParam(name="virtualCurrency", requirements="\w+", nullable=false, strict=true, description="Application virtual currency name")
     * @RequestParam(name="isRewarded", requirements="[01]", nullable=false, strict=true, description="Application offerwall type")
     * @RequestParam(name="isQuickReward", requirements="[01]", nullable=false, strict=true, description="Application reward type")
     * @RequestParam(name="exchangeRate", requirements="\d+", nullable=false, strict=true, description="Applications virtual currency exchange rate")
     */
    public function postApplicationAction(Request $request)
    {
        $publisher = $this->getDoctrine()
            ->getRepository('AppBundle:Publisher')
            ->find($request->request->get('publisherId'));
        $em = $this->getDoctrine()->getManager();

        if (!$publisher) {
            $response = new JsonResponse([], 404);
            $response->setContent('Publisher not found');

            return $response;
        }

        $application = new Application();
        $application->setPublisher($publisher);

        $form = $this->createForm(ApplicationType::class, $application, ['method' => $request->getMethod()]);
        $form->submit($request->request->all());

        if (count($this->get('validator')->validate($application))) {
            return new JsonResponse([], 400);
        }

        $em->persist($application);
        $em->flush();

        return new JsonResponse(['id' => $application->getId()], 201);
    }

    /**
     * @ApiDoc(
     *  resource = true,
     *  description="Update user aplication",
     *  views = { "default", "partner" },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="application id",
     *      }
     *  },
     *  statusCodes={
     *      200 = "Returned when updated",
     *      400 = "Returned when the form has errors or application already exists",
     *      403 = "Returned when api token is missing or incorrect",
     *      404 = "Returned when user not found",
     *  },
     *  headers={
     *      {
     *          "name"="API-AUTH-TOKEN",
     *          "description"="Authorization key",
     *          "required"="true"
     *      }
     *  },
     * )
     *
     * @RequestParam(name="isSandBox", requirements="[01]", nullable=true, strict=true, description="Application on sandbox mode")
     * @RequestParam(name="storeAppId", requirements="^\w*(\.\w+)+\w$|\d{9,}", nullable=true, strict=true, description="Application store id")
     * @RequestParam(name="virtualCurrency", requirements="\w+", nullable=true, strict=true, description="Application virtual currency name")
     * @RequestParam(name="isRewarded", requirements="[01]", nullable=true, strict=true, description="Application offerwall type")
     * @RequestParam(name="isQuickReward", requirements="[01]", nullable=true, strict=true, description="Application reward type")
     * @RequestParam(name="exchangeRate", requirements="\d+", nullable=true, strict=true, description="Applications virtual currency exchange rate")
     */
    public function patchApplicationAction(Application $id, Request $request)
    {
        $application = $id;
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(
            ApplicationType::class,
            $application,
            ['method' => $request->getMethod()]
        );
        $form->submit($request->request->all(), false);

        if (count($this->get('validator')->validate($application))) {
            return new JsonResponse([], 400);
        }

        $em->persist($application);
        $em->flush();

        return new JsonResponse([], 200);
    }
}