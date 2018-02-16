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
class StatsController extends Controller
{
    /**
     * @Route("/stats/{from}/{to}", options={"expose"=true}, name="stats",
     *      requirements={"from": "\d{4}-\d{1,2}-\d{1,2}", "to": "\d{4}-\d{1,2}-\d{1,2}"}))
     */
    public function indexAction($from = null, $to = null)
    {
        $params = [
            'fromDate' => $from ?? date("Y-m-d", time() - 60*60*24*30),
            'toDate' => $to ?? date("Y-m-d", time())
        ];

        return $this->render('stats/stats.html.twig', ['params' => $params]);
    }

    /**
     * @Route("/statsData/{from}/{to}", options={"expose"=true}, name="statsData",
     *      requirements={"from": "\d{4}-\d{1,2}-\d{1,2}", "to": "\d{4}-\d{1,2}-\d{1,2}"}))
     */
    public function dataAction(Request $request, $from, $to)
    {
        $params = ['fromDate' => $from, 'toDate' => $to];

        $rewards = $this->getDoctrine()
            ->getRepository('AppBundle:Reward')
            ->findStatsByParams($params);

        $publisherCount = $this->getDoctrine()
            ->getRepository('AppBundle:Reward')
            ->findPublishersCountByParams($params);

        $data = [
            "draw" =>  intval($request->query->get('draw')),
            "recordsTotal" => $publisherCount,
            "recordsFiltered" => $publisherCount,
            "data" => $rewards,
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/stats/platformPayoutTotal/{id}/{from}/{to}", options={"expose"=true}, name="platformPayoutTotal",
     *      requirements={"from": "\d{4}-\d{1,2}-\d{1,2}", "to": "\d{4}-\d{1,2}-\d{1,2}"}))
     */
    public function dataPlatformPayoutTotalAction(Request $request, $id, $from, $to)
    {
        $params = ['fromDate' => $from, 'toDate' => $to];

        return new JsonResponse($this->getDoctrine()
            ->getRepository('AppBundle:Reward')
            ->findPlatformTotalPayoutByParams($id, $params) ?? 0);
    }

    /**
     * @Route("/stats/platformRewardTotal/{id}/{from}/{to}", options={"expose"=true}, name="platformRewardTotal",
     *      requirements={"from": "\d{4}-\d{1,2}-\d{1,2}", "to": "\d{4}-\d{1,2}-\d{1,2}"}))
     */
    public function dataPlatformRewardTotalAction(Request $request, $id, $from, $to)
    {
        $params = ['fromDate' => $from, 'toDate' => $to];

        return new JsonResponse($this->getDoctrine()
            ->getRepository('AppBundle:Reward')
            ->findPlatformTotalRewardByParams($id, $params));
    }
}