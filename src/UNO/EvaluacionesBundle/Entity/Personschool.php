<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Personschool
 *
 * @ORM\Table(name="PersonSchool", indexes={@ORM\Index(name="fk_Person_has_School_School1_idx", columns={"schoolId"}), @ORM\Index(name="fk_Person_has_School_Person1_idx", columns={"personId"}), @ORM\Index(name="fk_Person_has_School_SchoolPeriod1_idx", columns={"schoolPeriodId"}), @ORM\Index(name="fk_Person_has_School_SchoolLevel1_idx", columns={"schoolLevelId"}), @ORM\Index(name="fk_Person_has_School_Profile1_idx", columns={"profileId"})})
 * @ORM\Entity
 */
class Personschool
{
    /**
     * @var \UNO\EvaluacionesBundle\Entity\Person
     *
     * @ORM\OneToOne(targetEntity="UNO\EvaluacionesBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="personId", referencedColumnName="personId", unique=true)
     * })
     */
    private $personid;

    /**
     * @var \UNO\EvaluacionesBundle\Entity\School
     *
     * @ORM\OneToOne(targetEntity="UNO\EvaluacionesBundle\Entity\School")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="schoolId", referencedColumnName="schoolId", unique=true)
     * })
     */
    private $schoolid;

    /**
     * @var \UNO\EvaluacionesBundle\Entity\Schoolperiod
     *
     * @ORM\OneToOne(targetEntity="UNO\EvaluacionesBundle\Entity\Schoolperiod")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="schoolPeriodId", referencedColumnName="schoolPeriodId", unique=true)
     * })
     */
    private $schoolperiodid;

    /**
     * @var \UNO\EvaluacionesBundle\Entity\Schoollevel
     *
     * @ORM\OneToOne(targetEntity="UNO\EvaluacionesBundle\Entity\Schoollevel")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="schoolLevelId", referencedColumnName="schoolLevelId", unique=true)
     * })
     */
    private $schoollevelid;

    /**
     * @var \UNO\EvaluacionesBundle\Entity\Profile
     *
     * @ORM\OneToOne(targetEntity="UNO\EvaluacionesBundle\Entity\Profile")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="profileId", referencedColumnName="profileId", unique=true)
     * })
     */
    private $profileid;



    /**
     * Set personid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Person $personid
     *
     * @return Personschool
     */
    public function setPersonid(\UNO\EvaluacionesBundle\Entity\Person $personid = null)
    {
        $this->personid = $personid;

        return $this;
    }

    /**
     * Get personid
     *
     * @return \UNO\EvaluacionesBundle\Entity\Person
     */
    public function getPersonid()
    {
        return $this->personid;
    }

    /**
     * Set schoolid
     *
     * @param \UNO\EvaluacionesBundle\Entity\School $schoolid
     *
     * @return Personschool
     */
    public function setSchoolid(\UNO\EvaluacionesBundle\Entity\School $schoolid = null)
    {
        $this->schoolid = $schoolid;

        return $this;
    }

    /**
     * Get schoolid
     *
     * @return \UNO\EvaluacionesBundle\Entity\School
     */
    public function getSchoolid()
    {
        return $this->schoolid;
    }

    /**
     * Set schoolperiodid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Schoolperiod $schoolperiodid
     *
     * @return Personschool
     */
    public function setSchoolperiodid(\UNO\EvaluacionesBundle\Entity\Schoolperiod $schoolperiodid = null)
    {
        $this->schoolperiodid = $schoolperiodid;

        return $this;
    }

    /**
     * Get schoolperiodid
     *
     * @return \UNO\EvaluacionesBundle\Entity\Schoolperiod
     */
    public function getSchoolperiodid()
    {
        return $this->schoolperiodid;
    }

    /**
     * Set schoollevelid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Schoollevel $schoollevelid
     *
     * @return Personschool
     */
    public function setSchoollevelid(\UNO\EvaluacionesBundle\Entity\Schoollevel $schoollevelid = null)
    {
        $this->schoollevelid = $schoollevelid;

        return $this;
    }

    /**
     * Get schoollevelid
     *
     * @return \UNO\EvaluacionesBundle\Entity\Schoollevel
     */
    public function getSchoollevelid()
    {
        return $this->schoollevelid;
    }

    /**
     * Set profileid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Profile $profileid
     *
     * @return Personschool
     */
    public function setProfileid(\UNO\EvaluacionesBundle\Entity\Profile $profileid = null)
    {
        $this->profileid = $profileid;

        return $this;
    }

    /**
     * Get profileid
     *
     * @return \UNO\EvaluacionesBundle\Entity\Profile
     */
    public function getProfileid()
    {
        return $this->profileid;
    }
}
