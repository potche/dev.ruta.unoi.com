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
     * @var integer
     *
     * @ORM\Column(name="personId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $personid;


}

