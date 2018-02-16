<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 17.03.17
 * Time: 16:53
 */
namespace PartnerApiBundle\Controller;

use AppBundle\Entity\Platform;
use AppBundle\Entity\Publisher;
use AppBundle\Entity\Application;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use HttpResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Form\PublisherType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class PublisherController extends FOSRestController
{
    /**
     * A resource
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns single publisher by id",
     *  views = { "default", "partner" },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="publisher id",
     *      }
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      403="Returned when api token is missing or incorrect",
     *      404={
     *        "Returned when publisher is not found",
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
    public function getPublisherAction(Publisher $id)
    {
        return $id;
    }

    /**
     * A resource
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Removes publisher",
     *  views = { "default", "partner" },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="publisher id",
     *      }
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when not deleted",
     *      403="Returned when api token is missing or incorrect",
     *      404={
     *        "Returned when publisher is not found",
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
    public function deletePublisherAction(Publisher $id)
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
     *  description="Posts publisher",
     *  views = { "default", "partner" },
     *  statusCodes={
     *      201 = "Returned when created",
     *      400 = "Returned when the form has errors or publisher already exists or urls not valid",
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
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="apiToken", requirements=".+", nullable=false, strict=true, description="Publisher token")
     * @RequestParam(name="name", requirements=".+", nullable=false, strict=true, description="Publisher username")
     * @RequestParam(name="domain", requirements="^\w*(\.\w+)+\w$", nullable=false, strict=true, description="Platform domain")
     * @RequestParam(name="isSkypeBotActive", requirements="[01]", nullable=false, strict=true, description="Publisher skype bot switch")
     * @return \FOS\RestBundle\View\View
     */
    public function postPublisherAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $publisher = new Publisher(
            $this->container->get('security.password_encoder'),
            $this->getDoctrine()->getRepository('AppBundle:Publisher')->findHighestId()
        );
        $platform = $this->getPlatformByDomain($request->request->get('domain'));

        $publisher->setPlatform($platform);

        $form = $this->createForm(PublisherType::class, $publisher, ['method' => $request->getMethod()]);
        $form->submit($request->request->all());

        /* if object has any errors or urls not valid */
        if (!$this->get('publisher.validator')->validate($publisher)) {
            return new JsonResponse([], 400);
        }

        $em->persist($platform);
        $em->persist($publisher);
        $em->flush();

        return new JsonResponse(['id' => $publisher->getId()], 201);
    }
    
    /**
     * @ApiDoc(
     *  resource = true,
     *  description="Update publisher",
     *  views = { "default", "partner" },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="publisher id",
     *      }
     *  },
     *  statusCodes={
     *      200 = "Returned when updated",
     *      400 = "Returned when the form has errors or urls not valid",
     *      403 = "Returned when api token is missing or incorrect",
     *      404 = "Returned when publisher not found",
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
     * @RequestParam(name="apiToken", requirements=".+", nullable=true, strict=true, description="Publisher token")
     * @RequestParam(name="name", requirements=".+", nullable=true, strict=true, description="Publisher username")
     * @RequestParam(name="isSkypeBotActive", requirements="[01]", nullable=true, strict=true, description="Publisher skype bot switch")
     * @RequestParam(name="skypeId", requirements=".+", nullable=true, strict=true, description="Publisher skype id. Send NULL to reset.")
     */
    public function patchPublisherAction(Publisher $id, Request $request)
    {
        $publisher = $id;
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(PublisherType::class, $publisher, ['method' => $request->getMethod()]);
        $form->submit($request->request->all(), false);

        /* ability to reset skype id */
        if ($publisher->getSkypeId() == "NULL" or $publisher->getSkypeId() == "null") {
            $publisher->setSkypeId(NULL);
        }

        /* if object has any errors or urls not valid or unique constraint fails */
        if (!$this->get('publisher.validator')->validate($publisher)) {
            return new JsonResponse([], 400);
        }

        $em->persist($publisher);
        $em->flush();

        return new JsonResponse([], 200);
    }

    private function getPlatformByDomain(string $domain) : Platform
    {
        $platform = $this->getDoctrine()
            ->getRepository('AppBundle:Platform')
            ->findOneBy(['domain' => $domain]);

        if (!$platform) {
            $platform = new Platform();
            $platform->setDomain($domain);
        }

        return $platform;
    }
}