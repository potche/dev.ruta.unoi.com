<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Optionxquestion
 *
 * @ORM\Table(name="OptionXQuestion", indexes={@ORM\Index(name="fk_Option_has_QuestionXSurvey_Option1_idx", columns={"Option_optionId"}), @ORM\Index(name="fk_OptionXQuestion_QuestionXSurvey1_idx", columns={"QuestionXSurvey_id"})})
 * @ORM\Entity
 */
class Optionxquestion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="order", type="integer", nullable=false)
     */
    private $order;

    /**
     * @var integer
     *
     * @ORM\Column(name="OptionXQuestion_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $optionxquestionId;

    /**
     * @var \UNO\EvaluacionesBundle\Entity\Option
     *
     * @ORM\ManyToOne(targetEntity="UNO\EvaluacionesBundle\Entity\Option")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Option_optionId", referencedColumnName="optionId")
     * })
     */
    private $optionOptionid;

    /**
     * @var \UNO\EvaluacionesBundle\Entity\Questionxsurvey
     *
     * @ORM\ManyToOne(targetEntity="UNO\EvaluacionesBundle\Entity\Questionxsurvey")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="QuestionXSurvey_id", referencedColumnName="QuestionxSurvey_id")
     * })
     */
    private $questionxsurvey;



    /**
     * Set order
     *
     * @param integer $order
     *
     * @return Optionxquestion
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Get optionxquestionId
     *
     * @return integer
     */
    public function getOptionxquestionId()
    {
        return $this->optionxquestionId;
    }

    /**
     * Set optionOptionid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Option $optionOptionid
     *
     * @return Optionxquestion
     */
    public function setOptionOptionid(\UNO\EvaluacionesBundle\Entity\Option $optionOptionid = null)
    {
        $this->optionOptionid = $optionOptionid;

        return $this;
    }

    /**
     * Get optionOptionid
     *
     * @return \UNO\EvaluacionesBundle\Entity\Option
     */
    public function getOptionOptionid()
    {
        return $this->optionOptionid;
    }

    /**
     * Set questionxsurvey
     *
     * @param \UNO\EvaluacionesBundle\Entity\Questionxsurvey $questionxsurvey
     *
     * @return Optionxquestion
     */
    public function setQuestionxsurvey(\UNO\EvaluacionesBundle\Entity\Questionxsurvey $questionxsurvey = null)
    {
        $this->questionxsurvey = $questionxsurvey;

        return $this;
    }

    /**
     * Get questionxsurvey
     *
     * @return \UNO\EvaluacionesBundle\Entity\Questionxsurvey
     */
    public function getQuestionxsurvey()
    {
        return $this->questionxsurvey;
    }
}
