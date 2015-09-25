<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Action
 *
 * @ORM\Table(name="Action")
 * @ORM\Entity
 */
class Action
{
    /**
     * @var string
     *
     * @ORM\Column(name="actionCode", type="string", length=45, nullable=false)
     */
    private $actioncode;

    /**
     * @var string
     *
     * @ORM\Column(name="Description", type="string", length=100, nullable=false)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="idAction", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idaction;



    /**
     * Set actioncode
     *
     * @param string $actioncode
     *
     * @return Action
     */
    public function setActioncode($actioncode)
    {
        $this->actioncode = $actioncode;

        return $this;
    }

    /**
     * Get actioncode
     *
     * @return string
     */
    public function getActioncode()
    {
        return $this->actioncode;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Action
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get idaction
     *
     * @return integer
     */
    public function getIdaction()
    {
        return $this->idaction;
    }
}
