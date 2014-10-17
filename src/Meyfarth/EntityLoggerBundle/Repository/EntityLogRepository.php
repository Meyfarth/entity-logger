<?php
/**
 * Created by PhpStorm.
 * User: Meyfarth
 * Date: 12/10/14
 * Time: 23:43
 */

namespace Meyfarth\EntityLoggerBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Meyfarth\EntityLoggerBundle\Service\EntityLoggerService;

class EntityLogRepository extends EntityRepository {


    /**
     * Get logs by page
     * @param int $page
     * @param int $nbByPage
     * @return Paginator
     */
    public function findLogsByPage($page = 1, $nbByPage = EntityLoggerService::LOGS_BY_PAGE){

        $qb = $this->createQueryBuilder('l')
            ->addOrderBy('l.date', 'DESC')
            ->setFirstResult(($page - 1) * $nbByPage)
            ->setMaxResults($nbByPage);
        return new Paginator($qb);
    }
} 