<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cgroup
 *
 * @ORM\Table(name="Cgroup")
 * @ORM\Entity
 */
class Cgroup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="groupId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $groupId;

    /**
     * @var string
     *
     * @ORM\Column(name="nameGroup", type="string", length=65)
     */
    private $nameGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="descGroup", type="string", length=255)
     */
    private $descGroup;


    /**
     * Get groupId
     *
     * @return integer
     */
    public function getGorupId()
    {
        return $this->groupId;
    }

    /**
     * Set nameGroup
     *
     * @param string $nameGroup
     *
     * @return Cgroup
     */
    public function setNameGroup($nameGroup)
    {
        $this->nameGroup = $nameGroup;

        return $this;
    }

    /**
     * Get nameGroup
     *
     * @return string
     */
    public function getNameGroup()
    {
        return $this->nameGroup;
    }

    /**
     * Set descGroup
     *
     * @param string $descGroup
     *
     * @return Cgroup
     */
    public function setDescProgram($descGroup)
    {
        $this->descGroup = $descGroup;

        return $this;
    }

    /**
     * Get descGroup
     *
     * @return string
     */
    public function getDescGroup()
    {
        return $this->descGroup;
    }

}

