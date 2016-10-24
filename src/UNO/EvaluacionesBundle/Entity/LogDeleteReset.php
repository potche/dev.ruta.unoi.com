<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LogDeleteReset
 *
 * @ORM\Table(name="LogDeleteReset")
 * @ORM\Entity
 */
class LogDeleteReset
{
    /**
     * @var integer
     *
     * @ORM\Column(name="logDeleteResetId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $logDeleteResetId;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length="255", nullable=false)
     */
    private $action;

    /**
     * @var integer
     *
     * @ORM\Column(name="personId", type="integer")
     */
    private $personId;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length="255")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length="255")
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="schoolId", type="integer")
     */
    private $schoolId;

    /**
     * @var string
     *
     * @ORM\Column(name="school", type="string", length="255")
     */
    private $school;

    /**
     * @var integer
     *
     * @ORM\Column(name="surveyId", type="integer")
     */
    private $surveyId;

    /**
     * @var string
     *
     * @ORM\Column(name="survey", type="string", length="255")
     */
    private $survey;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAction", type="datetime", nullable=false)
     */
    private $dateAction;

    /**
     * @var string
     *
     * @ORM\Column(name="ipCliente", type="string", length="45", nullable=false)
     */
    private $ipCliente;

    /**
     * @var integer
     *
     * @ORM\Column(name="executioner", type="integer")
     */
    private $executioner;

    /**
     * Get logDeleteResetId
     *
     * @return integer
     */
    public function getLogDeleteResetId()
    {
        return $this->logDeleteResetId;
    }

    /**
     * Set action
     *
     * @param string $action
     *
     * @return LogDeleteReset
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set personId
     *
     * @param integer $personId
     *
     * @return LogDeleteReset
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;

        return $this;
    }

    /**
     * Get personId
     *
     * @return integer
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return LogDeleteReset
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return LogDeleteReset
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set schoolId
     *
     * @param integer $schoolId
     *
     * @return LogDeleteReset
     */
    public function setSchoolId($schoolId)
    {
        $this->schoolId = $schoolId;

        return $this;
    }

    /**
     * Get schoolId
     *
     * @return integer
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
     * @return LogDeleteReset
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
     * Set surveyId
     *
     * @param integer $surveyId
     *
     * @return LogDeleteReset
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;

        return $this;
    }

    /**
     * Get surveyId
     *
     * @return integer
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }

    /**
     * Set survey
     *
     * @param string $survey
     *
     * @return LogDeleteReset
     */
    public function setSurvey($survey)
    {
        $this->survey = $survey;

        return $this;
    }

    /**
     * Get survey
     *
     * @return string
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * Set dateAction
     *
     * @param \DateTime $dateAction
     *
     * @return LogDeleteReset
     */
    public function setDateAction($dateAction)
    {
        $this->dateAction = $dateAction;

        return $this;
    }

    /**
     * Get dateAction
     *
     * @return \DateTime
     */
    public function getDateAction()
    {
        return $this->dateAction;
    }

    /**
     * Set ipCliente
     *
     * @param string $ipCliente
     *
     * @return LogDeleteReset
     */
    public function setIpCliente($ipCliente)
    {
        $this->ipCliente = $ipCliente;

        return $this;
    }

    /**
     * Get ipCliente
     *
     * @return string
     */
    public function getIpCliente()
    {
        return $this->ipCliente;
    }

    /**
     * Set executioner
     *
     * @param integer $executioner
     *
     * @return LogDeleteReset
     */
    public function setExecutioner($executioner)
    {
        $this->executioner = $executioner;

        return $this;
    }

    /**
     * Get executioner
     *
     * @return integer
     */
    public function getExecutioner()
    {
        return $this->executioner;
    }

}
