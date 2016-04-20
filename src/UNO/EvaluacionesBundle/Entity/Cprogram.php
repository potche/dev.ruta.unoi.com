<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cprogram
 *
 * @ORM\Table(name="Cprogram")
 * @ORM\Entity
 */
class Cprogram
{
    /**
     * @var integer
     *
     * @ORM\Column(name="programId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $programId;

    /**
     * @var string
     *
     * @ORM\Column(name="nameProgram", type="string", length=65)
     */
    private $nameProgram;

    /**
     * @var string
     *
     * @ORM\Column(name="descProgram", type="string", length=255)
     */
    private $descProgram;


    /**
     * Get programId
     *
     * @return integer
     */
    public function getProgramId()
    {
        return $this->programId;
    }

    /**
     * Set nameProgram
     *
     * @param string $nameProgram
     *
     * @return Cprogram
     */
    public function setNameProgram($nameProgram)
    {
        $this->nameProgram = $nameProgram;

        return $this;
    }

    /**
     * Get nameProgram
     *
     * @return string
     */
    public function getNameProgram()
    {
        return $this->nameProgram;
    }

    /**
     * Set descProgram
     *
     * @param string $descProgram
     *
     * @return Cprogram
     */
    public function setDescProgram($descProgram)
    {
        $this->descProgram = $descProgram;

        return $this;
    }

    /**
     * Get descProgram
     *
     * @return string
     */
    public function getDescProgram()
    {
        return $this->descProgram;
    }

}

