<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Profile
 *
 * @ORM\Table(name="Profile")
 * @ORM\Entity
 */
class Profile
{
    /**
     * @var string
     *
     * @ORM\Column(name="profileCode", type="string", length=45, nullable=false)
     */
    private $profilecode;

    /**
     * @var string
     *
     * @ORM\Column(name="profile", type="string", length=45, nullable=false)
     */
    private $profile;

    /**
     * @var integer
     *
     * @ORM\Column(name="profileId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $profileid;



    /**
     * Set profilecode
     *
     * @param string $profilecode
     *
     * @return Profile
     */
    public function setProfilecode($profilecode)
    {
        $this->profilecode = $profilecode;

        return $this;
    }

    /**
     * Get profilecode
     *
     * @return string
     */
    public function getProfilecode()
    {
        return $this->profilecode;
    }

    /**
     * Set profile
     *
     * @param string $profile
     *
     * @return Profile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return string
     */
    public function getProfile()
    {
        return $this->profile;
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
}
