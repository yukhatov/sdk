<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 11.05.17
 * Time: 16:08
 */
namespace AppBundle\EventListener;

use function MongoDB\BSON\toJSON;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;


use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
//use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use AppBundle\Entity\ApiResponse;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;

class ApiControllerListener
{
    private $em;

    /**
     * ApiControllerListener constructor.
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if ($event->getResponse()->getStatusCode() != 500) {
            return;
        }

        $params = null;

        if ($event->getRequest()->getMethod() != 'GET') {
            $params = $event->getRequest()->request->all();
            unset($params['_format']);

            if (count($params)) {
                $params = json_encode($params);
            } else {
                $params = null;
            }
        }

        $apiResponse = new ApiResponse();
        $apiResponse->setApplication($event->getRequest()->request->get('application'));
        $apiResponse->setUrl($event->getRequest()->getRequestUri());
        $apiResponse->setMethod($event->getRequest()->getMethod());
        $apiResponse->setParameters($params);
        $apiResponse->setResponseCode($event->getResponse()->getStatusCode());

        $this->em->persist($apiResponse);
        $this->em->flush();
    }
}