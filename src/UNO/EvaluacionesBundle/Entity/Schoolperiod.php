<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Schoolperiod
 *
 * @ORM\Table(name="SchoolPeriod")
 * @ORM\Entity
 */
class Schoolperiod
{
    /**
     * @var integer
     *
     * @ORM\Column(name="schoolPeriodId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $schoolperiodid;

    /**
     * @var string
     *
     * @ORM\Column(name="schoolPeriodCode", type="string", length=45, nullable=true)
     */
    private $schoolperiodcode;

    /**
     * @var string
     *
     * @ORM\Column(name="schoolPeriod", type="string", length=45, nullable=true)
     */
    private $schoolperiod;

    /**
     * @var string
     *
     * @ORM\Column(name="schoolPeriodActive", type="string", length=5, nullable=true)
     */
    private $schoolperiodactive;



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
     * Set schoolperiodcode
     *
     * @param string $schoolperiodcode
     *
     * @return Schoolperiod
     */
    public function setSchoolperiodcode($schoolperiodcode)
    {
        $this->schoolperiodcode = $schoolperiodcode;

        return $this;
    }

    /**
     * Get schoolperiodcode
     *
     * @return string
     */
    public function getSchoolperiodcode()
    {
        return $this->schoolperiodcode;
    }

    /**
     * Set schoolperiod
     *
     * @param string $schoolperiod
     *
     * @return Schoolperiod
     */
    public function setSchoolperiod($schoolperiod)
    {
        $this->schoolperiod = $schoolperiod;

        return $this;
    }

    /**
     * Get schoolperiod
     *
     * @return string
     */
    public function getSchoolperiod()
    {
        return $this->schoolperiod;
    }

    /**
     * Set schoolperiodactive
     *
     * @param string $schoolperiodactive
     *
     * @return Schoolperiod
     */
    public function setSchoolperiodactive($schoolperiodactive)
    {
        $this->schoolperiodactive = $schoolperiodactive;

        return $this;
    }

    /**
     * Get schoolperiodactive
     *
     * @return string
     */
    public function getSchoolperiodactive()
    {
        return $this->schoolperiodactive;
    }
}
