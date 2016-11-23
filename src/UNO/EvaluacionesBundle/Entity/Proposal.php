<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Proposal
 *
 * @ORM\Table(name="Proposal")
 * @ORM\Entity
 */

class Proposal
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="proposalId", type="integer")
     */

    private $proposalId;

    /**
     * @var integer
     *
     * @ORM\Column(name="schoolId", type="integer")
     */
    private $schoolId;

    /**
     * @var string
     *
     * @ORM\Column(name="school", type="string", length=65)
     */
    private $school;

    /**
     * @var string
     *
     * @ORM\Column(name="cp", type="string", length=5)
     */
    private $cp;

    /**
     * @var string
     *
     * @ORM\Column(name="colonia", type="string", length=65)
     */
    private $colonia;

    /**
     * @var string
     *
     * @ORM\Column(name="calle", type="string", length=65)
     */
    private $calle;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=5)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="nombreContacto", type="string", length=65)
     */
    private $nombreContacto;

    /**
     * @var string
     *
     * @ORM\Column(name="emailContacto", type="string", length=255)
     */
    private $emailContacto;

    /**
     * @var integer
     *
     * @ORM\Column(name="aulaDigital", type="integer")
     */
    private $aulaDigital;

    /**
     * @var integer
     *
     * @ORM\Column(name="makerCart", type="integer")
     */
    private $makerCart;

    /**
     * @var integer
     *
     * @ORM\Column(name="aulaMaker", type="integer")
     */
    private $aulaMaker;

    /**
     * @var integer
     *
     * @ORM\Column(name="proyector", type="integer")
     */
    private $proyector;

    /**
     * @var integer
     *
     * @ORM\Column(name="telepresencia", type="integer")
     */
    private $telepresencia;

    /**
     * @var integer
     *
     * @ORM\Column(name="aceleracon", type="integer")
     */
    private $aceleracon;

    /**
     * @var integer
     *
     * @ORM\Column(name="certificacion", type="integer")
     */
    private $certificacion;

    /**
     * @var integer
     *
     * @ORM\Column(name="desarrollo", type="integer")
     */
    private $desarrollo;

    /**
     * @var integer
     *
     * @ORM\Column(name="puntosTotales", type="integer")
     */
    private $puntosTotales;

    /**
     * @var integer
     *
     * @ORM\Column(name="puntosUsados", type="integer")
     */
    private $puntosUsados;

    /**
     * @var integer
     *
     * @ORM\Column(name="puntosSaldo", type="integer")
     */
    private $puntosSaldo;

    /**
     * @var float
     *
     * @ORM\Column(name="totalPesos", type="float")
     */
    private $totalPesos;

    /**
     * @var integer
     *
     * @ORM\Column(name="totalAños", type="integer")
     */
    private $totalAños;

    /**
     * @var float
     *
     * @ORM\Column(name="totalAportacion", type="float")
     */
    private $totalAportacion;

    /**
     * @var integer
     *
     * @ORM\Column(name="totalAlumnos", type="integer")
     */
    private $totalAlumnos;

    /**
     * @var integer
     *
     * @ORM\Column(name="porcentajeParticipacion", type="integer")
     */
    private $porcentajeParticipacion;

    /**
     * @var float
     *
     * @ORM\Column(name="precioVenta", type="float")
     */
    private $precioVenta;

    /**
     * @var integer
     *
     * @ORM\Column(name="vendedorId", type="integer")
     */
    private $vendedorId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateregister", type="datetime", nullable=false)
     */
    private $dateregister;

    /**
     * Get proposalId
     *
     * @return integer
     */
    public function getProposalId()
    {
        return $this->proposalId;
    }

    /**
     * Set schoolId
     *
     * @param string $schoolId
     *
     * @return Proposal
     */
    public function setSchoolId($schoolId)
    {
        $this->schoolId = $schoolId;

        return $this;
    }

    /**
     * Get schoolId
     *
     * @return string
     */
    public function getSchoolId()
    {
        return $this->schoolId;
    }

    /**
     * Set school
     *
     * @param string $school
     *
     * @return Proposal
     */
    public function setSchool($school)
    {
        $this->school = $school;

        return $this;
    }

    /**
     * Get school
     *
     * @return string
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * Set cp
     *
     * @param string $cp
     *
     * @return Proposal
     */
    public function setCp($cp)
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * Get cp
     *
     * @return string
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * Set colonia
     *
     * @param string $colonia
     *
     * @return Proposal
     */
    public function setColonia($colonia)
    {
        $this->colonia = $colonia;

        return $this;
    }

    /**
     * Get colonia
     *
     * @return string
     */
    public function getColonia()
    {
        return $this->colonia;
    }

    /**
     * Set calle
     *
     * @param string $calle
     *
     * @return Proposal
     */
    public function setCalle($calle)
    {
        $this->calle = $calle;

        return $this;
    }

    /**
     * Get calle
     *
     * @return string
     */
    public function getCalle()
    {
        return $this->calle;
    }

    /**
     * Set numero
     *
     * @param string $numero
     *
     * @return Proposal
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set nombreContacto
     *
     * @param string $nombreContacto
     *
     * @return Proposal
     */
    public function setNombreContacto($nombreContacto)
    {
        $this->nombreContacto = $nombreContacto;

        return $this;
    }

    /**
     * Get nombreContacto
     *
     * @return string
     */
    public function getNombreContacto()
    {
        return $this->nombreContacto;
    }

    /**
     * Set emailContacto
     *
     * @param string $emailContacto
     *
     * @return Proposal
     */
    public function setEmailContacto($emailContacto)
    {
        $this->emailContacto = $emailContacto;

        return $this;
    }

    /**
     * Get emailContacto
     *
     * @return string
     */
    public function getEmailContacto()
    {
        return $this->emailContacto;
    }

    /**
     * Set aulaDigital
     *
     * @param integer $aulaDigital
     *
     * @return Proposal
     */
    public function setAulaDigital($aulaDigital)
    {
        $this->aulaDigital = $aulaDigital;

        return $this;
    }

    /**
     * Get aulaDigital
     *
     * @return integer
     */
    public function getAulaDigital()
    {
        return $this->aulaDigital;
    }

    /**
     * Set makerCart
     *
     * @param integer $makerCart
     *
     * @return Proposal
     */
    public function setMakerCart($makerCart)
    {
        $this->makerCart = $makerCart;

        return $this;
    }

    /**
     * Get makerCart
     *
     * @return integer
     */
    public function getMakerCart()
    {
        return $this->makerCart;
    }

    /**
     * Set aulaMaker
     *
     * @param integer $aulaMaker
     *
     * @return Proposal
     */
    public function setAulaMaker($aulaMaker)
    {
        $this->aulaMaker = $aulaMaker;

        return $this;
    }

    /**
     * Get aulaMaker
     *
     * @return integer
     */
    public function getAulaMaker()
    {
        return $this->aulaMaker;
    }

    /**
     * Set proyector
     *
     * @param integer $proyector
     *
     * @return Proposal
     */
    public function setProyector($proyector)
    {
        $this->proyector = $proyector;

        return $this;
    }

    /**
     * Get proyector
     *
     * @return integer
     */
    public function getProyector()
    {
        return $this->proyector;
    }

    /**
     * Set telepresencia
     *
     * @param integer $telepresencia
     *
     * @return Proposal
     */
    public function setTelepresencia($telepresencia)
    {
        $this->telepresencia = $telepresencia;

        return $this;
    }

    /**
     * Get telepresencia
     *
     * @return integer
     */
    public function getTelepresencia()
    {
        return $this->telepresencia;
    }

    /**
     * Set aceleracon
     *
     * @param integer $aceleracon
     *
     * @return Proposal
     */
    public function setAceleracon($aceleracon)
    {
        $this->aceleracon = $aceleracon;

        return $this;
    }

    /**
     * Get aceleracon
     *
     * @return integer
     */
    public function getAceleracon()
    {
        return $this->aceleracon;
    }

    /**
     * Set certificacion
     *
     * @param integer $certificacion
     *
     * @return Proposal
     */
    public function setCertificacion($certificacion)
    {
        $this->certificacion = $certificacion;

        return $this;
    }

    /**
     * Get certificacion
     *
     * @return integer
     */
    public function getCertificacion()
    {
        return $this->certificacion;
    }

    /**
     * Set desarrollo
     *
     * @param integer $desarrollo
     *
     * @return Proposal
     */
    public function setDesarrollo($desarrollo)
    {
        $this->desarrollo = $desarrollo;

        return $this;
    }

    /**
     * Get desarrollo
     *
     * @return integer
     */
    public function getDesarrollo()
    {
        return $this->desarrollo;
    }

    /**
     * Set puntosTotales
     *
     * @param integer $puntosTotales
     *
     * @return Proposal
     */
    public function setPuntosTotales($puntosTotales)
    {
        $this->puntosTotales = $puntosTotales;

        return $this;
    }

    /**
     * Get puntosTotales
     *
     * @return integer
     */
    public function getPuntosTotales()
    {
        return $this->puntosTotales;
    }

    /**
     * Set puntosUsados
     *
     * @param integer $puntosUsados
     *
     * @return Proposal
     */
    public function setPuntosUsados($puntosUsados)
    {
        $this->puntosUsados = $puntosUsados;

        return $this;
    }

    /**
     * Get puntosUsados
     *
     * @return integer
     */
    public function getPuntosUsados()
    {
        return $this->puntosUsados;
    }

    /**
     * Set puntosSaldo
     *
     * @param integer $puntosSaldo
     *
     * @return Proposal
     */
    public function setPuntosSaldo($puntosSaldo)
    {
        $this->puntosSaldo = $puntosSaldo;

        return $this;
    }

    /**
     * Get puntosSaldo
     *
     * @return integer
     */
    public function getPuntosSaldo()
    {
        return $this->puntosSaldo;
    }

    /**
     * Set totalPesos
     *
     * @param integer $totalPesos
     *
     * @return Proposal
     */
    public function setTotalPesos($totalPesos)
    {
        $this->totalPesos = $totalPesos;

        return $this;
    }

    /**
     * Get totalPesos
     *
     * @return float
     */
    public function getTotalPesos()
    {
        return $this->totalPesos;
    }

    /**
     * Set totalAños
     *
     * @param integer $totalAños
     *
     * @return Proposal
     */
    public function setTotalAños($totalAños)
    {
        $this->totalAños = $totalAños;

        return $this;
    }

    /**
     * Get totalAños
     *
     * @return integer
     */
    public function getTotalAños()
    {
        return $this->totalAños;
    }

    /**
     * Set totalAportacion
     *
     * @param integer $totalAportacion
     *
     * @return Proposal
     */
    public function setTotalAportacion($totalAportacion)
    {
        $this->totalAportacion = $totalAportacion;

        return $this;
    }

    /**
     * Get totalAportacion
     *
     * @return float
     */
    public function getTotalAportacion()
    {
        return $this->totalAportacion;
    }

    /**
     * Set totalAlumnos
     *
     * @param integer $totalAlumnos
     *
     * @return Proposal
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
     * Set porcentajeParticipacion
     *
     * @param integer $porcentajeParticipacion
     *
     * @return Proposal
     */
    public function setPorcentajeParticipacion($porcentajeParticipacion)
    {
        $this->porcentajeParticipacion = $porcentajeParticipacion;

        return $this;
    }

    /**
     * Get porcentajeParticipacion
     *
     * @return integer
     */
    public function getPorcentajeParticipacion()
    {
        return $this->porcentajeParticipacion;
    }

    /**
     * Set precioVenta
     *
     * @param float $precioVenta
     *
     * @return Proposal
     */
    public function setPrecioVenta($precioVenta)
    {
        $this->precioVenta = $precioVenta;

        return $this;
    }

    /**
     * Get precioVenta
     *
     * @return float
     */
    public function getPrecioVenta()
    {
        return $this->precioVenta;
    }

    /**
     * Set vendedorId
     *
     * @param integer $vendedorId
     *
     * @return Proposal
     */
    public function setVendedorId($vendedorId)
    {
        $this->vendedorId = $vendedorId;

        return $this;
    }

    /**
     * Get vendedorId
     *
     * @return integer
     */
    public function getVendedorId()
    {
        return $this->vendedorId;
    }

    /**
     * Set dateregister
     *
     * @param \DateTime $dateregister
     *
     * @return Proposal
     */
    public function setDateregister($dateregister)
    {
        $this->dateregister = $dateregister;

        return $this;
    }

    /**
     * Get dateregister
     *
     * @return \DateTime
     */
    public function getDateregister()
    {
        return $this->dateregister;
    }
    
}
