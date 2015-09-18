<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Answer
 *
 * @ORM\Table(name="Answer", indexes={@ORM\Index(name="fk_Answer_Person1_idx", columns={"Person_personId"}), @ORM\Index(name="fk_Answer_OptionXQuestion1_idx", columns={"OptionXQuestion_id"})})
 * @ORM\Entity
 */
class Answer
{
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
     * @var integer
     *
     * @ORM\Column(name="answerId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $answerid;

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
     * @var \UNO\EvaluacionesBundle\Entity\Optionxquestion
     *
     * @ORM\ManyToOne(targetEntity="UNO\EvaluacionesBundle\Entity\Optionxquestion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="OptionXQuestion_id", referencedColumnName="OptionXQuestion_id")
     * })
     */
    private $optionxquestion;



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
     * Get answerid
     *
     * @return integer
     */
    public function getAnswerid()
    {
        return $this->answerid;
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

    /**
     * Set optionxquestion
     *
     * @param \UNO\EvaluacionesBundle\Entity\Optionxquestion $optionxquestion
     *
     * @return Answer
     */
    public function setOptionxquestion(\UNO\EvaluacionesBundle\Entity\Optionxquestion $optionxquestion = null)
    {
        $this->optionxquestion = $optionxquestion;

        return $this;
    }

    /**
     * Get optionxquestion
     *
     * @return \UNO\EvaluacionesBundle\Entity\Optionxquestion
     */
    public function getOptionxquestion()
    {
        return $this->optionxquestion;
    }
}
