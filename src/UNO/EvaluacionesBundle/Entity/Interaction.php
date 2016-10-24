<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Interaction
 *
 * @ORM\Table(name="Interaction")
 * @ORM\Entity
 */
class Interaction
{
    /**
     * @var integer
     *
     * @ORM\Column(name="interactionId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $interactionId;
    
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
     * Get interactionId
     * @return integer
     */
    public function getInteractionId()
    {
        return $this->interactionId;
    }

    /**
     * Set interactionId
     * @param integer $interactionId
     * @return Interaction
     */
    public function setInteractionId($interactionId)
    {
        $this->interactionId = $interactionId;
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
     * @return Interaction
     */
    public function setGradeId($gradeId)
    {
        $this->gradeId = $gradeId;
        return $this;
    }

    /**
     * Set groupId
     * @param string $groupId
     * @return Interaction
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
     * @return Interaction
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
     * @return Interaction
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
     * @return Interaction
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
     * @return Interaction
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

