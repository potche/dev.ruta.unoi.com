<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Option
 *
 * @ORM\Table(name="Option")
 * @ORM\Entity
 */
class Option
{
    /**
     * @var integer
     *
     * @ORM\Column(name="optionId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $optionid;

    /**
     * @var string
     *
     * @ORM\Column(name="option", type="string", length=250, nullable=false)
     */
    private $option;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="UNO\EvaluacionesBundle\Entity\Questionxsurvey", inversedBy="optionOptionid")
     * @ORM\JoinTable(name="optionxquestion",
     *   joinColumns={
     *     @ORM\JoinColumn(name="Option_optionId", referencedColumnName="optionId")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="QuestionXSurvey_Question_questionId", referencedColumnName="Question_questionId")
     *   }
     * )
     */
    private $questionxsurveySurveySurveyid;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->questionxsurveySurveySurveyid = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get optionid
     *
     * @return integer
     */
    public function getOptionid()
    {
        return $this->optionid;
    }

    /**
     * Set option
     *
     * @param string $option
     *
     * @return Option
     */
    public function setOption($option)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * Get option
     *
     * @return string
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Add questionxsurveySurveySurveyid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Questionxsurvey $questionxsurveySurveySurveyid
     *
     * @return Option
     */
    public function addQuestionxsurveySurveySurveyid(\UNO\EvaluacionesBundle\Entity\Questionxsurvey $questionxsurveySurveySurveyid)
    {
        $this->questionxsurveySurveySurveyid[] = $questionxsurveySurveySurveyid;

        return $this;
    }

    /**
     * Remove questionxsurveySurveySurveyid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Questionxsurvey $questionxsurveySurveySurveyid
     */
    public function removeQuestionxsurveySurveySurveyid(\UNO\EvaluacionesBundle\Entity\Questionxsurvey $questionxsurveySurveySurveyid)
    {
        $this->questionxsurveySurveySurveyid->removeElement($questionxsurveySurveySurveyid);
    }

    /**
     * Get questionxsurveySurveySurveyid
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestionxsurveySurveySurveyid()
    {
        return $this->questionxsurveySurveySurveyid;
    }
}
