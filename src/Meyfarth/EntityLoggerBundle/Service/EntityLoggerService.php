<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Meyfarth\EntityLoggerBundle\Service;

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
}
