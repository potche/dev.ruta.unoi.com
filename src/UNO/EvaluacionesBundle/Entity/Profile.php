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
     * @var integer
     *
     * @ORM\Column(name="profileId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $profileid;

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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="UNO\EvaluacionesBundle\Entity\Survey", inversedBy="profileProfileid")
     * @ORM\JoinTable(name="surveyxprofile",
     *   joinColumns={
     *     @ORM\JoinColumn(name="Profile_profileId", referencedColumnName="profileId")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="Survey_surveyId", referencedColumnName="surveyId")
     *   }
     * )
     */
    private $surveySurveyid;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->surveySurveyid = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add surveySurveyid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Survey $surveySurveyid
     *
     * @return Profile
     */
    public function addSurveySurveyid(\UNO\EvaluacionesBundle\Entity\Survey $surveySurveyid)
    {
        $this->surveySurveyid[] = $surveySurveyid;

        return $this;
    }

    /**
     * Remove surveySurveyid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Survey $surveySurveyid
     */
    public function removeSurveySurveyid(\UNO\EvaluacionesBundle\Entity\Survey $surveySurveyid)
    {
        $this->surveySurveyid->removeElement($surveySurveyid);
    }

    /**
     * Get surveySurveyid
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSurveySurveyid()
    {
        return $this->surveySurveyid;
    }
}
