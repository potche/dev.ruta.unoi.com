<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Optionapplication
 *
 * @ORM\Table(name="OptionApplication")
 * @ORM\Entity
 */
class Optionapplication
{
    /**
     * @var integer
     *
     * @ORM\Column(name="optionApplicationId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $optionApplicationId;

    /**
     * @var string
     *
     * @ORM\Column(name="nameOptionApplication", type="string", length=60, nullable=false)
     */
    private $nameOptionApplication;

    /**
     * @var string
     *
     * @ORM\Column(name="descOptionApplication", type="string", length=255, nullable=true)
     */
    private $descOptionApplication;

    /**
     * @var string
     *
     * @ORM\Column(name="ruteOptionApplication", type="string", length=60, nullable=false)
     */
    private $ruteOptionApplication;

    /**
     * @var boolean
     *
     * @ORM\Column(name="statusOptionApplication", type="boolean", nullable=false)
     */
    private $statusOptionApplication = 1;


    /**
     * Get optionApplicationId
     *
     * @return integer
     */
    public function getOptionApplicationId()
    {
        return $this->optionApplicationId;
    }

    /**
     * Set nameOptionApplication
     *
     * @param string $nameOptionApplication
     *
     * @return Optionapplication
     */
    public function setNameOptionApplication($nameOptionApplication)
    {
        $this->nameOptionApplication = $nameOptionApplication;

        return $this;
    }

    /**
     * Get nameOptionApplication
     *
     * @return string
     */
    public function getNameOptionApplication()
    {
        return $this->nameOptionApplication;
    }

    /**
     * Set descOptionApplication
     *
     * @param string $descOptionApplication
     *
     * @return Optionapplication
     */
    public function setDescOptionApplication($descOptionApplication)
    {
        $this->descOptionApplication = $descOptionApplication;

        return $this;
    }

    /**
     * Get descOptionApplication
     *
     * @return string
     */
    public function getDescOptionApplication()
    {
        return $this->descOptionApplication;
    }

    /**
     * Set ruteOptionApplication
     *
     * @param string $ruteOptionApplication
     *
     * @return Optionapplication
     */
    public function setRuteOptionApplication($ruteOptionApplication)
    {
        $this->ruteOptionApplication = $ruteOptionApplication;

        return $this;
    }

    /**
     * Get ruteOptionApplication
     *
     * @return string
     */
    public function getRuteOptionApplication()
    {
        return $this->ruteOptionApplication;
    }

    /**
     * Set statusOptionApplication
     *
     * @param integer $statusOptionApplication
     *
     * @return Optionapplication
     */
    public function setStatusOptionApplication($statusOptionApplication)
    {
        $this->statusOptionApplication = $statusOptionApplication;

        return $this;
    }

    /**
     * Get statusOptionApplication
     *
     * @return integer
     */
    public function getStatusOptionApplication()
    {
        return $this->statusOptionApplication;
    }

}
