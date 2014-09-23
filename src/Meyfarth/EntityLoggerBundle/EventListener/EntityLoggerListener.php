<?php

namespace Meyfarth\EntityLoggerBundle\EventListener;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\PersistentCollection;
use Meyfarth\EntityLoggerBundle\Entity\EntityLog;
use Meyfarth\EntityLoggerBundle\Entity\EntityLoggerInterface;
use Meyfarth\EntityLoggerBundle\Service\EntityLoggerService;
use Symfony\Component\DependencyInjection\Container;

/**
 * This class is part of Meyfarth\EntityLoggerBundle bundle
 * 
 * EntityLoggerListener listen to doctrine events (onFlush for update / delete, postPersist for insert)
 * It automatically adds a LogEntity row in database
 *
 * @author Meyfarth <garcia.sebastien@hotmail.fr>
 */
class EntityLoggerListener {
    
    private $config;
    private $container;

    public function __construct(Container $container){
        $this->container = $container;
    }
    
    /**
     * For updates and deletions
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args){
        // If enabled
        if(true === $this->config['enable']){
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
        if(true === $this->config['enable'] && true === $this->config['log']['create']){
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
     * @todo handle $typeLog and config (original_data + new log and modified_data + deletion)
     */
    private function createLog($entity, $typeLog, EntityManager $em, $isFlush){
        $uow = $em->getUnitOfWork();
        $metadata = $em->getClassMetadata(get_class($entity));

        $entityData = $this->parseData($uow->getOriginalEntityData($entity), $em);
        $entityChangeSet = $uow->getEntityChangeSet($entity);
        $fieldNames = $metadata->getFieldNames();


        if(in_array($this->config['log_type'], array('original_data', 'both'))){
            $data = $this->parseData($uow->getOriginalEntityData($entity), $em);

            $this->saveLog($entity, $data, $typeLog, $em, $isFlush);

        }

        if(in_array($this->config['log_type'], array('modified_data', 'both'))){
            // Get the current data for each field.
            $fieldNames = $metadata->getFieldNames();
            $data = array();

            // Convert data of each field
            foreach($fieldNames as $fieldName){
                $data[$fieldName] = $this->convertData($metadata->getFieldValue($entity, $fieldName));
            }

            $this->saveLog($entity, $data, $typeLog, $em, $isFlush);
        }
    }

    /**
     * Save the log depending on $entity and the $data
     * @param mixed $entity
     * @param array $data
     * @param integer $typeLog
     * @param EntityManager $em
     * @param bool $isFlush
     */
    private function saveLog($entity, array $data, $typeLog, EntityManager $em, $isFlush = false){

        $uow = $em->getUnitOfWork();

        $entityLog = new EntityLog();
        $entityLog->setData($data)
            ->setDate(new DateTime())
            ->setEntity($this->getEntityBundleShortcut($entity, $em))
            ->setTypeLog($typeLog)
            ->setForeignId($this->getEntityId($entity, $em));
        if(false !== $this->config['user_class'] && is_string($this->config['user_class'])){
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
     * @param EntityManager $em
     * @return array
     */
    private function parseData(array $entityData, EntityManager $em){
        // All collections will be saved as array of IDs
        foreach($entityData as $key => &$data){
            $entityData[$key] = $this->convertData($data);

        }
        return $entityData;
    }

    /**
     * convert a data to a string or a serialized object/array
     * @param mixed $data
     * @return string
     */
    private function convertData($data){
        if($data instanceof \DateTime){
            return $data->format(DateTime::W3C);
        }elseif(is_object($data) || is_array($data)){
            return serialize($data);
        }

        return $data;
    }
    
    /**
     * Get entity's ID or throw EntityLoggerNoIdException
     * @param mixed $entity
     * @param EntityManager $em
     * @return type
     */
    private function getEntityId($entity, EntityManager $em){
        $metadata = $em->getClassMetadata(get_class($entity));

        $ids = $metadata->getIdentifierValues($entity);
        return $ids;
    }


    /**
     * Get the shortcut name of an entity
     * @param $entity
     * @param EntityManager $em
     * @return string
     */
    private function getEntityBundleShortcut($entity, $em) {
        $path = explode('\Entity\\', $em->getClassMetadata(get_class($entity))->getName());
        return str_replace('\\', '', $path[0]).':'.$path[1];
    }
    
    /**
     * Sets the config
     * @param array $config
     */
    public function setConfig(array $config){
        $this->config = $config;
    }
}
