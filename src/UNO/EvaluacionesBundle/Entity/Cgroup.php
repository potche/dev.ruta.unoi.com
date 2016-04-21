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
     * @ORM\Column(name="groupId", type="string", length=65)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $groupId;

    /**
     * @var string
     *
     * @ORM\Column(name="nameGroup", type="string", length=255)
     */
    private $nameGroup;

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

}

