<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cgrade
 *
 * @ORM\Table(name="Cgrade")
 * @ORM\Entity
 */
class Cgrade
{
    /**
     * @var string
     *
     * @ORM\Column(name="gradeId", type="string", length=2)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $gradeId;

    /**
     * @var string
     *
     * @ORM\Column(name="nameGrade", type="string", length=65)
     */
    private $nameGrade;

    /**
     * @var string
     *
     * @ORM\Column(name="descGrade", type="string", length=255)
     */
    private $descGrade;


    /**
     * Get programId
     *
     * @return integer
     */
    public function getProgramId()
    {
        return $this->gradeId;
    }

    /**
     * Set nameGrade
     *
     * @param string $nameGrade
     *
     * @return Cgrade
     */
    public function setNameProgram($nameGrade)
    {
        $this->nameGrade = $nameGrade;

        return $this;
    }

    /**
     * Get nameGrade
     *
     * @return string
     */
    public function getNameGrade()
    {
        return $this->nameGrade;
    }

    /**
     * Set descGrade
     *
     * @param string $descGrade
     *
     * @return Cgrade
     */
    public function setDescGrade($descGrade)
    {
        $this->descGrade = $descGrade;

        return $this;
    }

    /**
     * Get descGrade
     *
     * @return string
     */
    public function getDescGrade()
    {
        return $this->descGrade;
    }

}

