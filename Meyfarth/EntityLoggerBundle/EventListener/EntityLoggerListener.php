<?php

namespace Meyfarth\EntityLoggerBundle\EventListener;

use DateTime;
use Doctrine\Entity;
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
    
    /**
     * For updates and deletions
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args){
        
        // For now, let's say all config are enabled
        $configEnabled = $configUpdate = $configDelete = true;
        
        // If enabled
        if($configEnabled === true){
            // Get all updates and deletes
            $em = $args->getEntityManager();
            $uow = $em->getUnitOfWork();
            
            if($configUpdate === true){
                foreach($uow->getScheduledEntityUpdates() as $entity){
                    // Get only entities that extends EntityLoggerInterface
                    if($entity instanceof EntityLoggerInterface){
                        $this->createLog($entity, EntityLoggerService::TYPE_UPDATE, $args->getEntityManager(), false);
                    }
                }
            }
        
            if($configDelete === true){
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
        $configEnabled = $configCreate = true;
        if($configEnabled === true && $configCreate === true){
            $entity = $args->getEntity();
            if ($entity instanceof EntityLoggerInterface) {
                // Calling the service creating and saving the log
                $this->createLog($entity, EntityLoggerService::TYPE_INSERT, $args->getEntityManager(), true);
            }
        }        
    }
    

    /**
     * 
     * @param Entity $entity
     * @param integer $typeLog
     * @param EntityManager $em
     * @param boolean $isFlush
     * @todo get user depending on configuration
     * @todo use doctrine notation MyAppMyBundle:MyEntity to store entity name
     */
    private function createLog(Entity $entity, $typeLog, EntityManager $em, $isFlush){
        $uow = $em->getUnitOfWork();
        $tableName = $em->getClassMetadata(get_class($entity))->getTableName();
        
         // Get data
        $id = $this->getEntityId($entity);

        $entityData = $this->parseData($uow->getOriginalEntityData($entity));

        $entityLog = new EntityLog();
        $entityLog->setData($entityData)
                ->setDate(new DateTime())
                ->setEntity($tableName)
                ->setForeignId($id);
        $user = $this->container->get('security.context')->getToken()->getUser();
        if(!is_null($user) && $user !== false){
            $entityLog->setUserLogged($user);
        }
        
        $em->persist($entityLog);
        $logMetadata = $em->getClassMetadata(get_class($mtgLog));
        $uow->computeChangeSet($logMetadata, $mtgLog);
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
    private function parseData($entityData){
        // All collections will be saved as array of IDs
        foreach($entityData as $key => &$data){
            if(is_object($data)){
                if($data instanceof PersistentCollection){
                    // Collections are not saved
                    unset($entityData[$key]);
                }else{
                    // Mapped entities are saved as IDs
                    $entityData[$key]= $this->getEntityId($data);
                }
            }
        }
        return $entityData;
    }
    
    /**
     * Get entity's ID or throw EntityLoggerNoIdException
     * @param Entity $entity
     * @return integer
     * @throws EntityLoggerNoIdException
     * @todo get ID based on doctrine metadata
     */
    private function getEntityId(Entity $entity){
        if(method_exists($entity, 'getId')){
            return $entity->getId();
        }else{
            throw new EntityLoggerMappedIdException(sprintf("your mapped entity '%s' should implement a getId() method to be logged with EntityLogger."), get_class($entity));
        }
    }
}
