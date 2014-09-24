<?php

namespace Meyfarth\EntityLoggerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * EntityLog
 */
class EntityLog
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var integer
     */
    private $typeLog;

    /**
     * @var string
     */
    private $entity;

    /**
     * @var integer
     */
    private $foreignId;

    /**
     * @var array
     */
    private $data;

    /**
     * @var UserInterface
     */

    private $userLogged;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return EntityLog
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set typeLog
     *
     * @param integer $typeLog
     * @return EntityLog
     */
    public function setTypeLog($typeLog)
    {
        $this->typeLog = $typeLog;

        return $this;
    }

    /**
     * Get typeLog
     *
     * @return integer 
     */
    public function getTypeLog()
    {
        return $this->typeLog;
    }
    
    /**
     * Set entity
     *
     * @param string $entity
     * @return EntityLog
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return string 
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set foreignId
     *
     * @param integer $foreignId
     * @return EntityLog
     */
    public function setForeignId($foreignId)
    {
        $this->foreignId = $foreignId;

        return $this;
    }

    /**
     * Get foreignId
     *
     * @return integer 
     */
    public function getForeignId()
    {
        return $this->foreignId;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return EntityLog
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return array 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return UserInterface
     */
    public function getUserLogged(){
        return $this->userLogged;
    }


    /**
     * @param UserInterface $userLogged
     * @return EntityLog $this
     */
    public function setUserLogged($userLogged){
        $this->userLogged = $userLogged;

        return $this;
    }
}
