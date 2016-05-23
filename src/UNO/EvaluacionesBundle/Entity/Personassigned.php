<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Personassigned
 *
 * @ORM\Table(name="PersonAssigned")
 * @ORM\Entity
 */
class Personassigned
{
    /**
     * @var integer
     *
     * @ORM\Column(name="personAssignedId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $personAssignedId;

    /**
     * @var integer
     *
     * @ORM\Column(name="schoolLevelId", type="integer")
     */
    private $schoolLevelId;

    /**
     * @var string
     *
     * @ORM\Column(name="gradeId", type="string", length=2)
     */
    private $gradeId;

    /**
     * @var string
     *
     * @ORM\Column(name="groupId", type="string", length=65)
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
     * Get personAssignedId
     *
     * @return integer
     */
    public function getPersonAssignedId()
    {
        return $this->personAssignedId;
    }

    /**
     * Set schoolLevelId
     *
     * @param integer $schoolLevelId
     *
     * @return Personassigned
     */
    public function setSchoolLevelId($schoolLevelId)
    {
        $this->schoolLevelId = $schoolLevelId;

        return $this;
    }

    /**
     * Get schoolLevelId
     *
     * @return integer
     */
    public function getSchoolLevelId()
    {
        return $this->schoolLevelId;
    }

    /**
     * Set gradeId
     *
     * @param string $gradeId
     *
     * @return Personassigned
     */
    public function setGradeId($gradeId)
    {
        $this->gradeId = $gradeId;

        return $this;
    }

    /**
     * Get gradeId
     *
     * @return string
     */
    public function getGradeId()
    {
        return $this->gradeId;
    }

    /**
     * Set groupId
     *
     * @param string $groupId
     *
     * @return Personassigned
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return string
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set programId
     *
     * @param integer $programId
     *
     * @return Personassigned
     */
    public function setProgramId($programId)
    {
        $this->programId = $programId;

        return $this;
    }

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
     * Set ppersonId
     *
     * @param integer $personId
     *
     * @return Personassigned
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
}

