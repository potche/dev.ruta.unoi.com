<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Answer
 *
 * @ORM\Table(name="ObservationAspects")
 * @ORM\Entity
 */
class ObservationAspects
{
    /**
     * @var integer
     *
     * @ORM\Column(name="observationAspectsId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $observationAspectsId;

    /**
     * @var string
     *
     * @ORM\Column(name="inicioRelevante", type="string", length=1000)
     */
    private $inicioRelevante;

    /**
     * @var string
     *
     * @ORM\Column(name="desarrolloRelevante", type="string", length=1000)
     */
    private $desarrolloRelevante;

    /**
     * @var string
     *
     * @ORM\Column(name="cierreRelevante", type="string", length=1000)
     */
    private $cierreRelevante;

    /**
     * @var string
     *
     * @ORM\Column(name="inicioMejorar", type="string", length=1000)
     */
    private $inicioMejorar;

    /**
     * @var string
     *
     * @ORM\Column(name="desarrolloMejorar", type="string", length=1000)
     */
    private $desarrolloMejorar;

    /**
     * @var string
     *
     * @ORM\Column(name="cierreMejorar", type="string", length=1000)
     */
    private $cierreMejorar;

    /**
     * @var integer
     *
     * @ORM\Column(name="observationId", type="integer")
     */
    private $observationId;

    /**
     * Get observationAspectsId
     *
     * @return integer
     */
    public function getObservationAspectsId()
    {
        return $this->observationAspectsId;
    }

    /**
     * Set inicioRelevante
     *
     * @param string $inicioRelevante
     *
     * @return ObservationAspects
     */
    public function setInicioRelevante($inicioRelevante)
    {
        $this->inicioRelevante = $inicioRelevante;

        return $this;
    }

    /**
     * Get inicioRelevante
     *
     * @return string
     */
    public function getInicioRelevante()
    {
        return $this->inicioRelevante;
    }

    /**
     * Set desarrolloRelevante
     *
     * @param string $desarrolloRelevante
     *
     * @return ObservationAspects
     */
    public function setDesarrolloRelevante($desarrolloRelevante)
    {
        $this->desarrolloRelevante = $desarrolloRelevante;

        return $this;
    }

    /**
     * Get desarrolloRelevante
     *
     * @return string
     */
    public function getDesarrolloRelevante()
    {
        return $this->desarrolloRelevante;
    }

    /**
     * Set cierreRelevante
     *
     * @param string $cierreRelevante
     *
     * @return ObservationAspects
     */
    public function setCierreRelevante($cierreRelevante)
    {
        $this->cierreRelevante = $cierreRelevante;

        return $this;
    }

    /**
     * Get cierreRelevante
     *
     * @return string
     */
    public function getCierreRelevante()
    {
        return $this->cierreRelevante;
    }

    /**
     * Set inicioMejorar
     *
     * @param string $inicioMejorar
     *
     * @return ObservationAspects
     */
    public function setInicioMejorar($inicioMejorar)
    {
        $this->inicioMejorar = $inicioMejorar;

        return $this;
    }

    /**
     * Get inicioMejorar
     *
     * @return string
     */
    public function getInicioMejorar()
    {
        return $this->inicioMejorar;
    }

    /**
     * Set desarrolloMejorar
     *
     * @param string $desarrolloMejorar
     *
     * @return ObservationAspects
     */
    public function setDesarrolloMejorar($desarrolloMejorar)
    {
        $this->desarrolloMejorar = $desarrolloMejorar;

        return $this;
    }

    /**
     * Get desarrolloMejorar
     *
     * @return string
     */
    public function getDesarrolloMejorar()
    {
        return $this->desarrolloMejorar;
    }

    /**
     * Set cierreMejorar
     *
     * @param string $cierreMejorar
     *
     * @return ObservationAspects
     */
    public function setCierreMejorar($cierreMejorar)
    {
        $this->cierreMejorar = $cierreMejorar;

        return $this;
    }

    /**
     * Get cierreMejorar
     *
     * @return string
     */
    public function getCierreMejorar()
    {
        return $this->cierreMejorar;
    }

    /**
     * Set observationId
     * @param integer $observationId
     * @return ObservationAnswer
     */
    public function setObservationId($observationId)
    {
        $this->observationId = $observationId;

        return $this;
    }

    /**
     * Get observationId
     *
     * @return integer
     */
    public function getObservationId()
    {
        return $this->observationId;
    }

}
