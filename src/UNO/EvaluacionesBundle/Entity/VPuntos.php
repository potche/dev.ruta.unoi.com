<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VPuntos
 *
 * @ORM\Table(name="VPuntos")
 * @ORM\Entity
 */

class VPuntos
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="colegioId", type="integer")
     */

    private $colegioId;

    /**
     * @var string
     *
     * @ORM\Column(name="colegio", type="string")
     */
    private $colegio;

    /**
     * @var integer
     *
     * @ORM\Column(name="matricula", type="integer")
     */
    private $matricula;

    /**
     * @var integer
     *
     * @ORM\Column(name="renovacionK", type="integer")
     */
    private $renovacionK;

    /**
     * @var integer
     *
     * @ORM\Column(name="renovacionP", type="integer")
     */
    private $renovacionP;

    /**
     * @var integer
     *
     * @ORM\Column(name="renovacionS", type="integer")
     */
    private $renovacionS;

    /**
     * @var integer
     *
     * @ORM\Column(name="totalAlumnos", type="integer")
     */
    private $totalAlumnos;

    /**
     * @var integer
     *
     * @ORM\Column(name="totalAluas", type="integer")
     */
    private $totalAluas;

    /**
     * @var float
     *
     * @ORM\Column(name="aulasDevengadas")
     */
    private $aulasDevengadas;

    /**
     * @var float
     *
     * @ORM\Column(name="alumnosPorAula")
     */
    private $alumnosPorAula;

    /**
     * @var integer
     *
     * @ORM\Column(name="puntosPorAulaDevengada", type="integer")
     */
    private $puntosPorAulaDevengada;

    /**
     * @var integer
     *
     * @ORM\Column(name="puntosPorAlumno")
     */
    private $puntosPorAlumno;

    /**
     * @var integer
     *
     * @ORM\Column(name="ciclos")
     */
    private $ciclos;

    /**
     * @var integer
     *
     * @ORM\Column(name="puntosOfrecerOld", type="integer")
     */
    private $puntosOfrecerOld;

    /**
     * @var integer
     *
     * @ORM\Column(name="puntosOfrecer", type="integer")
     */
    private $puntosOfrecer;

    /**
     * Get colegioId
     *
     * @return integer
     */
    public function getColegioId()
    {
        return $this->colegioId;
    }

    /**
     * Set colegio
     *
     * @param string $colegio
     *
     * @return VPuntos
     */
    public function setColegio($colegio)
    {
        $this->colegio = $colegio;

        return $this;
    }

    /**
     * Get colegio
     *
     * @return string
     */
    public function getColegio()
    {
        return $this->colegio;
    }

    /**
     * Set matricula
     *
     * @param integer $matricula
     *
     * @return VPuntos
     */
    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;

        return $this;
    }

    /**
     * Get matricula
     *
     * @return integer
     */
    public function getMatricula()
    {
        return $this->matricula;
    }

    /**
     * Set renovacionK
     *
     * @param integer $renovacionK
     *
     * @return VPuntos
     */
    public function setRenovacionK($renovacionK)
    {
        $this->renovacionK = $renovacionK;

        return $this;
    }

    /**
     * Get renovacionK
     *
     * @return integer
     */
    public function getRenovacionK()
    {
        return $this->renovacionK;
    }

    /**
     * Set renovacionP
     *
     * @param integer $renovacionP
     *
     * @return VPuntos
     */
    public function setRenovacionP($renovacionP)
    {
        $this->renovacionP = $renovacionP;

        return $this;
    }

    /**
     * Get renovacionP
     *
     * @return integer
     */
    public function getRenovacionP()
    {
        return $this->renovacionP;
    }

    /**
     * Set renovacionS
     *
     * @param integer $renovacionS
     *
     * @return VPuntos
     */
    public function setRenovacionS($renovacionS)
    {
        $this->renovacionS = $renovacionS;

        return $this;
    }

    /**
     * Get renovacionS
     *
     * @return integer
     */
    public function getRenovacionS()
    {
        return $this->renovacionS;
    }

    /**
     * Set totalAlumnos
     *
     * @param integer $totalAlumnos
     *
     * @return VPuntos
     */
    public function setTotalAlumnos($totalAlumnos)
    {
        $this->totalAlumnos = $totalAlumnos;

        return $this;
    }

    /**
     * Get totalAlumnos
     *
     * @return integer
     */
    public function getTotalAlumnos()
    {
        return $this->totalAlumnos;
    }

    /**
     * Set totalAluas
     *
     * @param integer $totalAluas
     *
     * @return VPuntos
     */
    public function setTotalAluas($totalAluas)
    {
        $this->totalAluas = $totalAluas;

        return $this;
    }

    /**
     * Get totalAluas
     *
     * @return integer
     */
    public function getTotalAluas()
    {
        return $this->totalAluas;
    }

    /**
     * Set aulasDevengadas
     *
     * @param float $aulasDevengadas
     *
     * @return VPuntos
     */
    public function setAulasDevengadas($aulasDevengadas)
    {
        $this->aulasDevengadas = $aulasDevengadas;

        return $this;
    }

    /**
     * Get aulasDevengadas
     *
     * @return float
     */
    public function getAulasDevengadas()
    {
        return $this->aulasDevengadas;
    }

    /**
     * Set calle
     *
     * @param float $alumnosPorAula
     *
     * @return VPuntos
     */
    public function setAlumnosPorAula($alumnosPorAula)
    {
        $this->alumnosPorAula = $alumnosPorAula;

        return $this;
    }

    /**
     * Get alumnosPorAula
     *
     * @return float
     */
    public function getAlumnosPorAula()
    {
        return $this->alumnosPorAula;
    }

    /**
     * Set puntosPorAulaDevengada
     *
     * @param integer $puntosPorAulaDevengada
     *
     * @return VPuntos
     */
    public function setPuntosPorAulaDevengada($puntosPorAulaDevengada)
    {
        $this->puntosPorAulaDevengada = $puntosPorAulaDevengada;

        return $this;
    }

    /**
     * Get puntosPorAulaDevengada
     *
     * @return integer
     */
    public function getPuntosPorAulaDevengada()
    {
        return $this->puntosPorAulaDevengada;
    }

    /**
     * Set puntosPorAlumno
     *
     * @param integer $puntosPorAlumno
     *
     * @return VPuntos
     */
    public function setPuntosPorAlumno($puntosPorAlumno)
    {
        $this->puntosPorAlumno = $puntosPorAlumno;

        return $this;
    }

    /**
     * Get puntosPorAlumno
     *
     * @return integer
     */
    public function getPuntosPorAlumno()
    {
        return $this->puntosPorAlumno;
    }

    /**
     * Set ciclos
     *
     * @param integer $ciclos
     *
     * @return VPuntos
     */
    public function setCiclos($ciclos)
    {
        $this->ciclos = $ciclos;

        return $this;
    }

    /**
     * Get ciclos
     *
     * @return integer
     */
    public function getCiclos()
    {
        return $this->ciclos;
    }

    /**
     * Set puntosOfrecerOld
     *
     * @param integer $puntosOfrecerOld
     *
     * @return VPuntos
     */
    public function setPuntosOfrecerOld($puntosOfrecerOld)
    {
        $this->puntosOfrecerOld = $puntosOfrecerOld;

        return $this;
    }

    /**
     * Get puntosOfrecerOld
     *
     * @return integer
     */
    public function getPuntosOfrecerOld()
    {
        return $this->puntosOfrecerOld;
    }

    /**
     * Set puntosOfrecer
     *
     * @param integer $puntosOfrecer
     *
     * @return VPuntos
     */
    public function setPuntosOfrecer($puntosOfrecer)
    {
        $this->puntosOfrecer = $puntosOfrecer;

        return $this;
    }

    /**
     * Get puntosOfrecer
     *
     * @return integer
     */
    public function getPuntosOfrecer()
    {
        return $this->puntosOfrecer;
    }

}
