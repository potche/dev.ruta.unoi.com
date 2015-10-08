<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Questionxsurvey
 *
 * @ORM\Table(name="QuestionXSurvey", indexes={@ORM\Index(name="fk_Encuesta_has_Pregunta_Pregunta1_idx", columns={"Question_questionId"}), @ORM\Index(name="fk_Encuesta_has_Pregunta_Encuesta1_idx", columns={"Survey_surveyId"})})
 * @ORM\Entity
 */
class Questionxsurvey
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
     * @ORM\Column(name="QuestionxSurvey_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $questionxsurveyId;

    /**
     * @var \UNO\EvaluacionesBundle\Entity\Question
     *
     * @ORM\ManyToOne(targetEntity="UNO\EvaluacionesBundle\Entity\Question")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Question_questionId", referencedColumnName="questionId")
     * })
     */
    private $questionQuestionid;

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
     * Set order
     *
     * @param integer $order
     *
     * @return Questionxsurvey
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
     * Get questionxsurveyId
     *
     * @return integer
     */
    public function getQuestionxsurveyId()
    {
        return $this->questionxsurveyId;
    }

    /**
     * Set questionQuestionid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Question $questionQuestionid
     *
     * @return Questionxsurvey
     */
    public function setQuestionQuestionid(\UNO\EvaluacionesBundle\Entity\Question $questionQuestionid = null)
    {
        $this->questionQuestionid = $questionQuestionid;

        return $this;
    }

    /**
     * Get questionQuestionid
     *
     * @return \UNO\EvaluacionesBundle\Entity\Question
     */
    public function getQuestionQuestionid()
    {
        return $this->questionQuestionid;
    }

    /**
     * Set surveySurveyid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Survey $surveySurveyid
     *
     * @return Questionxsurvey
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
}
