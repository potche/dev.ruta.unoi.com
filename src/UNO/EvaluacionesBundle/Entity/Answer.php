<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Answer
 *
 * @ORM\Table(name="Answer", indexes={@ORM\Index(name="fk_OptionsXQuestion_has_Person_Person1_idx", columns={"Person_personId"}), @ORM\Index(name="fk_OptionsXQuestion_has_Person_OptionsXQuestion1_idx", columns={"OptionXQuestion_optionId", "OptionXQuestion_surveyId", "OptionXQuestion_questionId"})})
 * @ORM\Entity
 */
class Answer
{
    /**
     * @var integer
     *
     * @ORM\Column(name="answerId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $answerid;

    /**
     * @var string
     *
     * @ORM\Column(name="answer", type="string", length=250, nullable=false)
     */
    private $answer;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=300, nullable=true)
     */
    private $comment;

    /**
     * @var \UNO\EvaluacionesBundle\Entity\Optionxquestion
     *
     * @ORM\ManyToOne(targetEntity="UNO\EvaluacionesBundle\Entity\Optionxquestion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="OptionXQuestion_optionId", referencedColumnName="Option_optionId"),
     *   @ORM\JoinColumn(name="OptionXQuestion_surveyId", referencedColumnName="QuestionXSurvey_Survey_surveyId"),
     *   @ORM\JoinColumn(name="OptionXQuestion_questionId", referencedColumnName="QuestionXSurvey_Question_questionId")
     * })
     */
    private $optionxquestionOptionid;

    /**
     * @var \UNO\EvaluacionesBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="UNO\EvaluacionesBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Person_personId", referencedColumnName="personId")
     * })
     */
    private $personPersonid;



    /**
     * Get answerid
     *
     * @return integer
     */
    public function getAnswerid()
    {
        return $this->answerid;
    }

    /**
     * Set answer
     *
     * @param string $answer
     *
     * @return Answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Answer
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set optionxquestionOptionid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Optionxquestion $optionxquestionOptionid
     *
     * @return Answer
     */
    public function setOptionxquestionOptionid(\UNO\EvaluacionesBundle\Entity\Optionxquestion $optionxquestionOptionid = null)
    {
        $this->optionxquestionOptionid = $optionxquestionOptionid;

        return $this;
    }

    /**
     * Get optionxquestionOptionid
     *
     * @return \UNO\EvaluacionesBundle\Entity\Optionxquestion
     */
    public function getOptionxquestionOptionid()
    {
        return $this->optionxquestionOptionid;
    }

    /**
     * Set personPersonid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Person $personPersonid
     *
     * @return Answer
     */
    public function setPersonPersonid(\UNO\EvaluacionesBundle\Entity\Person $personPersonid = null)
    {
        $this->personPersonid = $personPersonid;

        return $this;
    }

    /**
     * Get personPersonid
     *
     * @return \UNO\EvaluacionesBundle\Entity\Person
     */
    public function getPersonPersonid()
    {
        return $this->personPersonid;
    }
}
