<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Meyfarth\EntityLoggerBundle\Service;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Description of EntityLoggerService
 *
 * @author Meyfarth
 * @todo method to compare 2 logs from the same entity
 */
class EntityLoggerService {
    const TYPE_INSERT = 0;
    const TYPE_UPDATE = 1;
    const TYPE_DELETE = 2;


    const LOGS_BY_PAGE = 50;

    private $logByPage;

    public function __construct(){
        $this->logByPage = self::LOGS_BY_PAGE;
    }


    /**
     * Set the number of logs by page (default 50)
     * @param $nb
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function setNbLogByPage($nb){
        if(!is_int($nb)){
            throw new InvalidArgumentException("The number must be an int (%s given)", $nb);
        }

        $this->logByPage = $nb;
    }

    /**
     * Get the number of logs by page
     * @return int
     *
     */
    public function getNbLogByPage(){
        if(is_null($this->logByPage) || !is_int($this->logByPage)){
            $this->logByPage = self::LOGS_BY_PAGE;
        }

        return $this->logByPage;
    }
}
