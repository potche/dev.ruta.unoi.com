<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Log
 *
 * @ORM\Table(name="Log", indexes={@ORM\Index(name="fk_action_idx", columns={"Action_idAction"})})
 * @ORM\Entity
 */
class Log
{
    /**
     * @var integer
     *
     * @ORM\Column(name="Person_personId", type="integer", nullable=false)
     */
    private $personPersonid;

    /**
     * @var integer
     *
     * @ORM\Column(name="Survey_surveyId", type="integer", nullable=false)
     */
    private $surveySurveyid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="logId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $logid;

    /**
     * @var \UNO\EvaluacionesBundle\Entity\Action
     *
     * @ORM\ManyToOne(targetEntity="UNO\EvaluacionesBundle\Entity\Action")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Action_idAction", referencedColumnName="idAction")
     * })
     */
    private $actionaction;



    /**
     * Set personPersonid
     *
     * @param integer $personPersonid
     *
     * @return Log
     */
    public function setPersonPersonid($personPersonid)
    {
        $this->personPersonid = $personPersonid;

        return $this;
    }

    /**
     * Get personPersonid
     *
     * @return integer
     */
    public function getPersonPersonid()
    {
        return $this->personPersonid;
    }

    /**
     * Set surveySurveyid
     *
     * @param integer $surveySurveyid
     *
     * @return Log
     */
    public function setSurveySurveyid($surveySurveyid)
    {
        $this->surveySurveyid = $surveySurveyid;

        return $this;
    }

    /**
     * Get surveySurveyid
     *
     * @return integer
     */
    public function getSurveySurveyid()
    {
        return $this->surveySurveyid;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Log
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get logid
     *
     * @return integer
     */
    public function getLogid()
    {
        return $this->logid;
    }

    /**
     * Set actionaction
     *
     * @param \UNO\EvaluacionesBundle\Entity\Action $actionaction
     *
     * @return Log
     */
    public function setActionaction(\UNO\EvaluacionesBundle\Entity\Action $actionaction = null)
    {
        $this->actionaction = $actionaction;

        return $this;
    }

    /**
     * Get actionaction
     *
     * @return \UNO\EvaluacionesBundle\Entity\Action
     */
    public function getActionaction()
    {
        return $this->actionaction;
    }
}
