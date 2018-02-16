<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 06.06.17
 * Time: 12:37
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

/**
 * Application controller.
 *
 * @Route("process")
 */
class ProcessController extends Controller
{
    /**
     * @Route("/run")
     * @Method("GET")
     */
    public function runAction()
    {
        if ($this->get('database.compressor')->compressRewards()) {
            return new Response('Compressed', 200);
        }

        return new Response('Everything ok', 200);
    }
}