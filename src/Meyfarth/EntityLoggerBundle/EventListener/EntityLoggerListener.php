<?php

namespace Meyfarth\EntityLoggerBundle\EventListener;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\PersistentCollection;
use Meyfarth\EntityLoggerBundle\Entity\EntityLog;
use Meyfarth\EntityLoggerBundle\Entity\EntityLoggerInterface;
use Meyfarth\EntityLoggerBundle\Exception\EntityLoggerMappedIdException;
use Meyfarth\EntityLoggerBundle\Exception\EntityLoggerNoIdException;
use Meyfarth\EntityLoggerBundle\Service\EntityLoggerService;

/**
 * This class is part of Meyfarth\EntityLoggerBundle bundle
 * 
 * EntityLoggerListener listen to doctrine events (onFlush for update / delete, postPersist for insert)
 * It automatically adds a LogEntity row in database
 *
 * @author Meyfarth <garcia.sebastien@hotmail.fr>
 * @todo do stuff depending on user's configuration
 */
class EntityLoggerListener {
    
    private $config;
    
    /**
     * For updates and deletions
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args){
        // If enabled
        if(true === $this->config['enabled']){
            // Get all updates and deletes
            $uow = $args->getEntityManager()->getUnitOfWork();
            
            if(true === $this->config['log']['update']){
                foreach($uow->getScheduledEntityUpdates() as $entity){
                    // Get only entities that extends EntityLoggerInterface
                    if($entity instanceof EntityLoggerInterface){
                        $this->createLog($entity, EntityLoggerService::TYPE_UPDATE, $args->getEntityManager(), false);
                    }
                }
            }
        
            if(true === $this->config['log']['delete']){
                foreach($uow->getScheduledEntityDeletions() as $entity){
                    if($entity instanceof EntityLoggerInterface){
                        $this->createLog($entity, EntityLoggerService::TYPE_DELETE, $args->getEntityManager(), false);
                    }
                }
            }
        }
    }
    
    /**
     * For insertions
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args){
        // For now, let's say all config are enabled
        if(true === $this->config['enabled'] && true === $this->config['log']['create']){
            $entity = $args->getEntity();
            if ($entity instanceof EntityLoggerInterface) {
                // Calling the service creating and saving the log
                $this->createLog($entity, EntityLoggerService::TYPE_INSERT, $args->getEntityManager(), true);
            }
        }        
    }
    

    /**
     * 
     * @param $entity
     * @param integer $typeLog
     * @param EntityManager $em
     * @param boolean $isFlush
     * @todo get user depending on configuration
     * @todo use doctrine notation MyAppMyBundle:MyEntity to store entity name
     */
    private function createLog($entity, $typeLog, EntityManager $em, $isFlush){
        $uow = $em->getUnitOfWork();
        $metadata = $em->getClassMetadata(get_class($entity));
        
        $tableName = $metadata->getTableName();
            
        $entityData = $this->parseData($uow->getOriginalEntityData($entity), $em);

        $entityLog = new EntityLog();
        $entityLog->setData($entityData)
                ->setDate(new DateTime())
                ->setEntity($tableName)
                ->setTypeLog($typeLog)
                ->setForeignId($this->getEntityId($entity, $em));
        if(true === $this->config['log_current_user']){
            
            if($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')){
                $user = $this->container->get('security.context')->getToken()->getUser();
                $entityLog->setUserLogged($user);
            }
        }
        
        $em->persist($entityLog);
        $logMetadata = $em->getClassMetadata(get_class($entityLog));
        $uow->computeChangeSet($logMetadata, $entityLog);
        if($isFlush){
            // Flush (only for insertions since updates / deletions are not flushed yet)
            $em->flush();
        }
    }
    
    /**
     * Parse data. If a data is an entity, get its ID instead, if it's an ArrayCollection, get an array of IDs
     * @param array $entityData
     * @return array
     */
    private function parseData($entityData, EntityManager $em){
        // All collections will be saved as array of IDs
        foreach($entityData as $key => &$data){
            if(is_object($data)){
                if($data instanceof PersistentCollection){
                    // Collections are not saved yet
                    unset($entityData[$key]);
                }elseif($data instanceof \DateTime){
                    $data = $data->format(DateTime::ISO8601);
                }else{
                    // Mapped entities are saved as IDs
                    $entityData[$key]= $this->getEntityId($data, $em);
                }
            }
        }
        return $entityData;
    }
    
    /**
     * Get entity's ID or throw EntityLoggerNoIdException
     * @param type $entity
     * @param \Doctrine\ORM\EntityManager $em
     * @return type
     */
    private function getEntityId($entity, EntityManager $em){
        $metadata = $em->getClassMetadata(get_class($entity));

        $ids = $metadata->getIdentifierValues($entity);
        return $ids;
    }
}
