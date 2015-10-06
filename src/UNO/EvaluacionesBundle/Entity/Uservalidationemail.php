<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Uservalidationemail
 *
 * @ORM\Entity
 */
class Uservalidationemail
{
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable=false)
     */
    private $code;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateRegister", type="datetime", nullable=false)
     */
    private $dateregister;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text", length=65535, nullable=false)
     */
    private $data;

    /**
     * @var integer
     *
     * @ORM\Column(name="idUserValidationEmail", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $iduservalidationemail;

    /**
     * @var integer
     * @ORM\Column(name="personId", type="integer", nullable=false)
     *
     */
    private $personid;



    /**
     * Set email
     *
     * @param string $email
     *
     * @return Uservalidationemail
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
     * Set code
     *
     * @param integer $code
     *
     * @return Uservalidationemail
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set dateregister
     *
     * @param \DateTime $dateregister
     *
     * @return Uservalidationemail
     */
    public function setDateregister($dateregister)
    {
        $this->dateregister = $dateregister;

        return $this;
    }

    /**
     * Get dateregister
     *
     * @return \DateTime
     */
    public function getDateregister()
    {
        return $this->dateregister;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return Uservalidationemail
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get iduservalidationemail
     *
     * @return integer
     */
    public function getIduservalidationemail()
    {
        return $this->iduservalidationemail;
    }

    /**
     * Set personid
     *
     * @param integer $personid
     *
     * @return Uservalidationemail
     */
    public function setPersonid($personid)
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
}
