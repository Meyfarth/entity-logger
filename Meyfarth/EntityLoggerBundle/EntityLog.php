<?php

namespace Meyfarth\EntityLoggerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var integer
     */
    private $userId;

    /**
     * @var \Meyfarth\EntityLogger\Model\UserLoggedInterface
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
     * Set userId
     *
     * @param integer $userId
     * @return EntityLog
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set userLogged
     *
     * @param \Meyfarth\EntityLogger\Model\UserLoggedInterface $userLogged
     * @return EntityLog
     */
    public function setUserLogged(\Meyfarth\EntityLogger\Model\UserLoggedInterface $userLogged = null)
    {
        $this->userLogged = $userLogged;

        return $this;
    }

    /**
     * Get userLogged
     *
     * @return \Meyfarth\EntityLogger\Model\UserLoggedInterface 
     */
    public function getUserLogged()
    {
        return $this->userLogged;
    }
}