<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Personschool
 *
 * @ORM\Entity
 */
class Personschool
{
    /**
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $schoolperiodid;

    /**
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $schoollevelid;

    /**
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $schoolid;

    /**
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $profileid;

    /**
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     */
    private $personid;



    /**
     * Set schoolperiodid
     *
     * @param integer $schoolperiodid
     *
     * @return Personschool
     */
    public function setSchoolperiodid($schoolperiodid)
    {
        $this->schoolperiodid = $schoolperiodid;

        return $this;
    }

    /**
     * Get schoolperiodid
     *
     * @return integer
     */
    public function getSchoolperiodid()
    {
        return $this->schoolperiodid;
    }

    /**
     * Set schoollevelid
     *
     * @param integer $schoollevelid
     *
     * @return Personschool
     */
    public function setSchoollevelid($schoollevelid)
    {
        $this->schoollevelid = $schoollevelid;

        return $this;
    }

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
     * Set schoolid
     *
     * @param integer $schoolid
     *
     * @return Personschool
     */
    public function setSchoolid($schoolid)
    {
        $this->schoolid = $schoolid;

        return $this;
    }

    /**
     * Get schoolid
     *
     * @return integer
     */
    public function getSchoolid()
    {
        return $this->schoolid;
    }

    /**
     * Set profileid
     *
     * @param integer $profileid
     *
     * @return Personschool
     */
    public function setProfileid($profileid)
    {
        $this->profileid = $profileid;

        return $this;
    }

    /**
     * Get profileid
     *
     * @return integer
     */
    public function getProfileid()
    {
        return $this->profileid;
    }

    /**
     * Set personid
     *
     * @param integer $personid
     *
     * @return Personschool
     */
    public function setPersonid($personid)
    {
        $this->personid = $personid;

        return $this;
    }

    /**
     * Get personid
     *
     * @return integer
     */
    public function getPersonid()
    {
        return $this->personid;
    }
}
