<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Answerhistory
 *
 * @ORM\Entity
 */
class Answerhistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="answerId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $answerId;

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
     * @ORM\Column(name="Person_personId", type="integer")
     */
    private $personPersonId;

    /**
     *@var integer
     *
     * @ORM\Column(name="OptionXQuestion_id", type="integer")
     *
     */
    private $optionXquestionId;

    /**
     *@var \DateTime
     *
     * @ORM\Column(name="dateHistory", type="datetime", nullable=true)
     *
     */
    private $dateHistory;

    /**
     * Set answerId
     *
     * @param integer $answerId
     *
     * @return Answerhistory
     */
    public function setAnswerId($answerId)
    {
        $this->answerId = $answerId;

        return $this;
    }

    /**
     * Get answerId
     *
     * @return integer
     */
    public function getAnswerId()
    {
        return $this->answerId;
    }

    /**
     * Set answer
     *
     * @param string $answer
     *
     * @return Answerhistory
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
     * @return Answerhistory
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
     * Set personPersonId
     *
     * @param integer $personPersonId
     *
     * @return Answerhistory
     */
    public function setPersonPersonId($personPersonId)
    {
        $this->personPersonId = $personPersonId;

        return $this;
    }

    /**
     * Get personPersonId
     *
     * @return integer
     */
    public function getPersonPersonId()
    {
        return $this->personPersonId;
    }

    /**
     * Set optionXquestionId
     *
     * @param integer $optionXquestionId
     *
     * @return Answerhistory
     */
    public function setOptionXquestionId($optionXquestionId)
    {
        $this->optionXquestionId = $optionXquestionId;

        return $this;
    }

    /**
     * Get optionXquestionId
     *
     * @return integer
     */
    public function getOptionXquestionId()
    {
        return $this->optionXquestionId;
    }

    /**
     * Set dateHistory
     *
     * @param \DateTime $dateHistory
     *
     * @return Answerhistory
     */
    public function setDateHistory($dateHistory)
    {
        $this->dateHistory = $dateHistory;

        return $this;
    }

    /**
     * Get dateHistory
     *
     * @return \DateTime
     */
    public function getDateHistory()
    {
        return $this->dateHistory;
    }
}
