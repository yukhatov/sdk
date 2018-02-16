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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Application controller.
 */
class BotStatsController extends Controller
{
    /**
     * @Route("/bot_stats/", options={"expose"=true}, name="bot_stats")
     */
    public function indexAction()
    {
        $stats = $this->getDoctrine()
            ->getRepository("AppBundle:PublisherStatistic")
            ->findStatistic();

        return $this->render('stats/bot_stats.html.twig', ['stats' => $stats]);
    }
}