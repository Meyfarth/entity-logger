<?php
/**
 * Created by PhpStorm.
 * User: Meyfarth
 * Date: 12/10/14
 * Time: 23:04
 */

namespace Meyfarth\EntityLoggerBundle\Controller;


use Meyfarth\EntityLoggerBundle\Entity\EntityLog;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LogController extends Controller {

    /**
     * List all logs
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction($page = 1){

        $nbByPage = $this->get('meyfarth.service.log_service')->getNbLogByPage();
        $logs = $this->getDoctrine()->getRepository('MeyfarthEntityLoggerBundle:EntityLog')->findLogsByPage($page, $nbByPage);

        $nbLogsTot = count($logs);
        $nbPages = ceil($nbLogsTot / $nbByPage);


        return $this->render('MeyfarthEntityLoggerBundle:Log:list.html.twig', array(
            'logs' => $logs,
            'page' => $page,
            'nbByPage' => $nbByPage,
            'nbPages' => $nbPages,
        ));
    }
}