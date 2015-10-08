<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * School
 *
 * @ORM\Table(name="School")
 * @ORM\Entity
 */
class School
{
    /**
     * @var string
     *
     * @ORM\Column(name="schoolCode", type="string", length=45, nullable=false)
     */
    private $schoolcode;

    /**
     * @var string
     *
     * @ORM\Column(name="school", type="string", length=255, nullable=false)
     */
    private $school;

    /**
     * @var integer
     *
     * @ORM\Column(name="countryId", type="integer", nullable=false)
     */
    private $countryid;

    /**
     * @var string
     *
     * @ORM\Column(name="countryCode", type="string", length=4, nullable=false)
     */
    private $countrycode;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=45, nullable=false)
     */
    private $country;

    /**
     * @var integer
     *
     * @ORM\Column(name="languageId", type="integer", nullable=false)
     */
    private $languageid;

    /**
     * @var string
     *
     * @ORM\Column(name="languageCode", type="string", length=4, nullable=false)
     */
    private $languagecode;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=45, nullable=false)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="timeZone", type="string", length=45, nullable=false)
     */
    private $timezone;

    /**
     * @var integer
     *
     * @ORM\Column(name="schoolId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $schoolid;



    /**
     * Set schoolcode
     *
     * @param string $schoolcode
     *
     * @return School
     */
    public function setSchoolcode($schoolcode)
    {
        $this->schoolcode = $schoolcode;

        return $this;
    }

    /**
     * Get schoolcode
     *
     * @return string
     */
    public function getSchoolcode()
    {
        return $this->schoolcode;
    }

    /**
     * Set school
     *
     * @param string $school
     *
     * @return School
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
     * Set countryid
     *
     * @param integer $countryid
     *
     * @return School
     */
    public function setCountryid($countryid)
    {
        $this->countryid = $countryid;

        return $this;
    }

    /**
     * Get countryid
     *
     * @return integer
     */
    public function getCountryid()
    {
        return $this->countryid;
    }

    /**
     * Set countrycode
     *
     * @param string $countrycode
     *
     * @return School
     */
    public function setCountrycode($countrycode)
    {
        $this->countrycode = $countrycode;

        return $this;
    }

    /**
     * Get countrycode
     *
     * @return string
     */
    public function getCountrycode()
    {
        return $this->countrycode;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return School
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set languageid
     *
     * @param integer $languageid
     *
     * @return School
     */
    public function setLanguageid($languageid)
    {
        $this->languageid = $languageid;

        return $this;
    }

    /**
     * Get languageid
     *
     * @return integer
     */
    public function getLanguageid()
    {
        return $this->languageid;
    }

    /**
     * Set languagecode
     *
     * @param string $languagecode
     *
     * @return School
     */
    public function setLanguagecode($languagecode)
    {
        $this->languagecode = $languagecode;

        return $this;
    }

    /**
     * Get languagecode
     *
     * @return string
     */
    public function getLanguagecode()
    {
        return $this->languagecode;
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return School
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set timezone
     *
     * @param string $timezone
     *
     * @return School
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
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
}
