<?php

namespace Meyfarth\EntityLoggerBundle\EventListener;



use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Sensio\Bundle\FrameworkExtraBundle\EventListener\SecurityListener;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Common\EventSubscriber;

class DynamicUserMappingSubscriber implements EventSubscriber
{
    const ENTITY_CLASS = 'Meyfarth\EntityLoggerBundle\Entity\EntityLog';

    private $userClass;



    public function setUserClass($userClass){
        $this->userClass = $userClass;
    }


    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata,
        );
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        if($this->userClass !== false){
            // the $metadata is the whole mapping info for this class
            $metadata = $eventArgs->getClassMetadata();

            if ($metadata->getName() != self::ENTITY_CLASS) {
                return;
            }



            $em = $eventArgs->getEntityManager();
            $userMetadata = $em->getClassMetadata($this->userClass);

            $namingStrategy = $eventArgs
                ->getEntityManager()
                ->getConfiguration()
                ->getNamingStrategy()
            ;

            $metadata->mapManyToOne(array(
                    'targetEntity' => $userMetadata->getName(),
                    'fieldName' => 'userLogged',
                    'joinColumns' => array(array('name' => 'user_id')),
                ));
        }

    }
}