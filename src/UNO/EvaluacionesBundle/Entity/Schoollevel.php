<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Schoollevel
 *
 * @ORM\Table(name="SchoolLevel")
 * @ORM\Entity
 */
class Schoollevel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="schoolLevelId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $schoollevelid;

    /**
     * @var string
     *
     * @ORM\Column(name="schoolLevelCode", type="string", length=5, nullable=false)
     */
    private $schoollevelcode;

    /**
     * @var string
     *
     * @ORM\Column(name="schoolLevel", type="string", length=45, nullable=false)
     */
    private $schoollevel;

    /**
     * @var integer
     *
     * @ORM\Column(name="schoolLevelCompanyId", type="integer", nullable=false)
     */
    private $schoollevelcompanyid;

    /**
     * @var integer
     *
     * @ORM\Column(name="colegio_nivel_ciclo_id", type="integer", nullable=false)
     */
    private $colegioNivelCicloId;



    /**
     * Get schoollevelid
     *
     * @return integer
     */
    public function getSchoollevelid()
    {
        return $this->schoollevelid;
    }

    /**
     * Set schoollevelcode
     *
     * @param string $schoollevelcode
     *
     * @return Schoollevel
     */
    public function setSchoollevelcode($schoollevelcode)
    {
        $this->schoollevelcode = $schoollevelcode;

        return $this;
    }

    /**
     * Get schoollevelcode
     *
     * @return string
     */
    public function getSchoollevelcode()
    {
        return $this->schoollevelcode;
    }

    /**
     * Set schoollevel
     *
     * @param string $schoollevel
     *
     * @return Schoollevel
     */
    public function setSchoollevel($schoollevel)
    {
        $this->schoollevel = $schoollevel;

        return $this;
    }

    /**
     * Get schoollevel
     *
     * @return string
     */
    public function getSchoollevel()
    {
        return $this->schoollevel;
    }

    /**
     * Set schoollevelcompanyid
     *
     * @param integer $schoollevelcompanyid
     *
     * @return Schoollevel
     */
    public function setSchoollevelcompanyid($schoollevelcompanyid)
    {
        $this->schoollevelcompanyid = $schoollevelcompanyid;

        return $this;
    }

    /**
     * Get schoollevelcompanyid
     *
     * @return integer
     */
    public function getSchoollevelcompanyid()
    {
        return $this->schoollevelcompanyid;
    }

    /**
     * Set colegioNivelCicloId
     *
     * @param integer $colegioNivelCicloId
     *
     * @return Schoollevel
     */
    public function setColegioNivelCicloId($colegioNivelCicloId)
    {
        $this->colegioNivelCicloId = $colegioNivelCicloId;

        return $this;
    }

    /**
     * Get colegioNivelCicloId
     *
     * @return integer
     */
    public function getColegioNivelCicloId()
    {
        return $this->colegioNivelCicloId;
    }
}
