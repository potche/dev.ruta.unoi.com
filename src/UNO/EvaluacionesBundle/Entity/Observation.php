<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Observation
 *
 * @ORM\Table(name="Observation")
 * @ORM\Entity
 */
class Observation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="observationId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $observationId;

    /**
     * @var integer
     *
     * @ORM\Column(name="surveyId", type="integer")
     */
    private $surveyId;

    /**
     * @var string
     *
     * @ORM\Column(name="gradeId", type="string", length="2")
     */
    private $gradeId;

    /**
     * @var string
     *
     * @ORM\Column(name="groupId", type="string", length="65")
     */
    private $groupId;

    /**
     * @var integer
     *
     * @ORM\Column(name="programId", type="integer")
     */
    private $programId;

    /**
     * @var integer
     *
     * @ORM\Column(name="personId", type="integer")
     */
    private $personId;

    /**
     * @var integer
     *
     * @ORM\Column(name="schoolId", type="integer")
     */
    private $schoolId;

    /**
     * @var integer
     *
     * @ORM\Column(name="coachId", type="integer")
     */
    private $coachId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="datetime")
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finish", type="datetime")
     */
    private $finish;

    /**
     * Get observationId
     * @return integer
     */
    public function getObservationId()
    {
        return $this->observationId;
    }

    /**
     * Set observationId
     * @param integer $observationId
     * @return Observation
     */
    public function setObservationId($observationId)
    {
        $this->observationId = $observationId;
        return $this;
    }

    /**
     * Get surveyId
     * @return integer
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }

    /**
     * Set surveyId
     * @param integer $surveyId
     * @return Observation
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;
        return $this;
    }

    /**
     * Get gradeId
     * @return string
     */
    public function getGradeId()
    {
        return $this->gradeId;
    }

    /**
     * Set gradeId
     * @param string $gradeId
     * @return Observation
     */
    public function setGradeId($gradeId)
    {
        $this->gradeId = $gradeId;
        return $this;
    }

    /**
     * Set groupId
     * @param string $groupId
     * @return Observation
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
        return $this;
    }

    /**
     * Get groupId
     * @return string
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Get programId
     * @return integer
     */
    public function getProgramId()
    {
        return $this->programId;
    }

    /**
     * Set programId
     * @param integer $programId
     * @return Observation
     */
    public function setProgramId($programId)
    {
        $this->programId = $programId;
        return $this;
    }

    /**
     * Get personId
     * @return integer
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Set personId
     * @param integer $personId
     * @return Observation
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
        return $this;
    }

    /**
     * Get schoolId
     * @return integer
     */
    public function getSchoolId()
    {
        return $this->schoolId;
    }

    /**
     * Set schoolId
     * @param integer $schoolId
     * @return Observation
     */
    public function setSchoolId($schoolId)
    {
        $this->schoolId = $schoolId;
        return $this;
    }

    /**
     * Get coachId
     * @return integer
     */
    public function getCoachId()
    {
        return $this->coachId;
    }

    /**
     * Set coachId
     * @param integer $coachId
     * @return Observation
     */
    public function setCoachId($coachId)
    {
        $this->coachId = $coachId;
        return $this;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     *
     * @return Observation
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set finish
     *
     * @param \DateTime $finish
     *
     * @return Observation
     */
    public function setFinish($finish)
    {
        $this->finish = $finish;

        return $this;
    }

    /**
     * Get finish
     *
     * @return \DateTime
     */
    public function getFinish()
    {
        return $this->finish;
    }
}

