<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Person
 *
 * @ORM\Table(name="Person")
 * @ORM\Entity
 */
class Person
{
    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=255, nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=64, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255, nullable=false)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=1, nullable=false)
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="birthDay", type="string", length=2, nullable=false)
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="birthMonth", type="string", length=2, nullable=false)
     */
    private $birthmonth;

    /**
     * @var string
     *
     * @ORM\Column(name="birthYear", type="string", length=4, nullable=false)
     */
    private $birthyear;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

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
     * @var boolean
     *
     * @ORM\Column(name="admin", type="boolean", nullable=false)
     */
    private $admin = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = 1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mailing", type="boolean", nullable=false)
     */
    private $mailing = 1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="tourEnabled", type="boolean", nullable=false)
     */
    private $tourenabled = 1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="assigned", type="boolean", nullable=false)
     */
    private $assigned = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastLogin", type="datetime", nullable=false)
     */
    private $lastLogin;

    /**
     * @var integer
     * @ORM\personid
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="personId", type="integer")
     */

    private $personid;



    /**
     * Set user
     *
     * @param string $user
     *
     * @return Person
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
     * Set password
     *
     * @param string $password
     *
     * @return Person
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Person
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
     * Set surname
     *
     * @param string $surname
     *
     * @return Person
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return Person
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set birthday
     *
     * @param string $birthday
     *
     * @return Person
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return string
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set birthmonth
     *
     * @param string $birthmonth
     *
     * @return Person
     */
    public function setBirthmonth($birthmonth)
    {
        $this->birthmonth = $birthmonth;

        return $this;
    }

    /**
     * Get birthmonth
     *
     * @return string
     */
    public function getBirthmonth()
    {
        return $this->birthmonth;
    }

    /**
     * Set birthyear
     *
     * @param string $birthyear
     *
     * @return Person
     */
    public function setBirthyear($birthyear)
    {
        $this->birthyear = $birthyear;

        return $this;
    }

    /**
     * Get birthyear
     *
     * @return string
     */
    public function getBirthyear()
    {
        return $this->birthyear;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Person
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set languageid
     *
     * @param integer $languageid
     *
     * @return Person
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
     * @return Person
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
     * @return Person
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
     * @return Person
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
     * Set admin
     *
     * @param boolean $admin
     *
     * @return Person
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return boolean
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Person
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set mailing
     *
     * @param boolean $mailing
     *
     * @return Person
     */
    public function setMailing($mailing)
    {
        $this->mailing = $mailing;

        return $this;
    }

    /**
     * Get mailing
     *
     * @return boolean
     */
    public function getMailing()
    {
        return $this->mailing;
    }

    /**
     * Set tourenabled
     *
     * @param boolean $tourenabled
     *
     * @return Person
     */
    public function setTourenabled($tourenabled)
    {
        $this->tourenabled = $tourenabled;

        return $this;
    }

    /**
     * Get tourenabled
     *
     * @return boolean
     */
    public function getTourenabled()
    {
        return $this->tourenabled;
    }

    /**
     * Set assigned
     *
     * @param boolean $assigned
     *
     * @return Person
     */
    public function setAssigned($assigned)
    {
        $this->assigned = $assigned;

        return $this;
    }

    /**
     * Get assigned
     *
     * @return boolean
     */
    public function getAssigned()
    {
        return $this->assigned;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     *
     * @return Person
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

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
