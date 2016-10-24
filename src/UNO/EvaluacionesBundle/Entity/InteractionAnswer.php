<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InteractionAnswer
 *
 * @ORM\Table(name="InteractionAnswer")
 * @ORM\Entity
 */
class InteractionAnswer
{
    /**
     * @var integer
     *
     * @ORM\Column(name="interactionAnswerId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $interactionAnswerId;

    /**
     * @var integer
     *
     * @ORM\Column(name="questionId", type="integer")
     */
    private $questionId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="answer", type="boolean")
     */
    private $answer;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=300)
     */
    private $comment;

    /**
     * @var integer
     *
     * @ORM\Column(name="personId", type="integer")
     */
    private $personId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateRecord", type="datetime")
     */
    private $dateRecord;

    /**
     * @var integer
     *
     * @ORM\Column(name="interactionId", type="integer")
     */
    private $interactionId;

    /**
     * Get interactionAnswerId
     *
     * @return integer
     */
    public function getInteractionAnswerId()
    {
        return $this->interactionAnswerId;
    }

    /**
     * Set questionId
     *
     * @param integer $questionId
     *
     * @return InteractionAnswer
     */
    public function setQuestionId($questionId)
    {
        $this->questionId = $questionId;

        return $this;
    }

    /**
     * Get questionId
     *
     * @return integer
     */
    public function getQuestionId()
    {
        return $this->questionId;
    }

    /**
     * Set answer
     *
     * @param string $answer
     *
     * @return InteractionAnswer
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
     * @return InteractionAnswer
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
     * Set personId
     * @param integer $personId
     * @return InteractionAnswer
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;

        return $this;
    }

    /**
     * Get personId
     *
     * @return integer
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Set dateRecord
     * @param \DateTime $dateRecord
     * @return InteractionAnswer
     */
    public function setDateRecord($dateRecord)
    {
        $this->dateRecord = $dateRecord;

        return $this;
    }

    /**
     * Get dateRecord
     *
     * @return \DateTime
     */
    public function getDateRecord()
    {
        return $this->dateRecord;
    }

    /**
     * Set interactionId
     * @param integer $interactionId
     * @return InteractionAnswer
     */
    public function setInteractionId($interactionId)
    {
        $this->interactionId = $interactionId;

        return $this;
    }

    /**
     * Get interactionId
     *
     * @return integer
     */
    public function getInteractionId()
    {
        return $this->interactionId;
    }

}
