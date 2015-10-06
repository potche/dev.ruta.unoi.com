<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Surveyxprofile
 *
 * @ORM\Table(name="SurveyXProfile", indexes={@ORM\Index(name="fk_Profile_has_Survey_Survey1_idx", columns={"Survey_surveyId"}), @ORM\Index(name="fk_Profile_has_Survey_Profile1_idx", columns={"Profile_profileId"})})
 * @ORM\Entity
 */
class Surveyxprofile
{
    /**
     * @var integer
     *
     * @ORM\Column(name="SurveyxProfile_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $surveyxprofileId;

    /**
     * @var \UNO\EvaluacionesBundle\Entity\Survey
     *
     * @ORM\ManyToOne(targetEntity="UNO\EvaluacionesBundle\Entity\Survey")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Survey_surveyId", referencedColumnName="surveyId")
     * })
     */
    private $surveySurveyid;

    /**
     * @var \UNO\EvaluacionesBundle\Entity\Profile
     *
     * @ORM\ManyToOne(targetEntity="UNO\EvaluacionesBundle\Entity\Profile")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Profile_profileId", referencedColumnName="profileId")
     * })
     */
    private $profileProfileid;

    /**
     * @var integer
     *
     * @ORM\Column(name="schoolLevelId", type="integer", nullable=false)
     */
    private $schoollevelid;

    /**
     * Get surveyxprofileId
     *
     * @return integer
     */
    public function getSurveyxprofileId()
    {
        return $this->surveyxprofileId;
    }

    /**
     * Set surveySurveyid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Survey $surveySurveyid
     *
     * @return Surveyxprofile
     */
    public function setSurveySurveyid(\UNO\EvaluacionesBundle\Entity\Survey $surveySurveyid = null)
    {
        $this->surveySurveyid = $surveySurveyid;

        return $this;
    }

    /**
     * Get surveySurveyid
     *
     * @return \UNO\EvaluacionesBundle\Entity\Survey
     */
    public function getSurveySurveyid()
    {
        return $this->surveySurveyid;
    }

    /**
     * Set profileProfileid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Profile $profileProfileid
     *
     * @return Surveyxprofile
     */
    public function setProfileProfileid(\UNO\EvaluacionesBundle\Entity\Profile $profileProfileid = null)
    {
        $this->profileProfileid = $profileProfileid;

        return $this;
    }

    /**
     * Get profileProfileid
     *
     * @return \UNO\EvaluacionesBundle\Entity\Profile
     */
    public function getProfileProfileid()
    {
        return $this->profileProfileid;
    }

    /**
     * Set schoollevelid
     *
     * @param integer $schoollevelid
     *
     * @return Schoollevelid
     */

    public function setSchoollevelid($schoollevelid) {

        $this->schoollevelid = $schoollevelid;

        return $this;
    }

    /**
     * Get schoollevelid
     * @return integer
     */

    public function getSchoollevelid() {

        return $this->schoollevelid;
    }
}
