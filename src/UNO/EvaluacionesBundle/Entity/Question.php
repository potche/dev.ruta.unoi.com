<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Table(name="Question", indexes={@ORM\Index(name="fk_Pregunta_Categoria1_idx", columns={"Subcategory_subcategoryId"})})
 * @ORM\Entity
 */
class Question
{
    /**
     * @var integer
     *
     * @ORM\Column(name="questionId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $questionid;

    /**
     * @var string
     *
     * @ORM\Column(name="question", type="string", length=500, nullable=false)
     */
    private $question;

    /**
     * @var boolean
     *
     * @ORM\Column(name="required", type="boolean", nullable=false)
     */
    private $required;

    /**
     * @var \UNO\EvaluacionesBundle\Entity\Subcategory
     *
     * @ORM\ManyToOne(targetEntity="UNO\EvaluacionesBundle\Entity\Subcategory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Subcategory_subcategoryId", referencedColumnName="subcategoryId")
     * })
     */
    private $subcategorySubcategoryid;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="UNO\EvaluacionesBundle\Entity\Survey", mappedBy="questionQuestionid")
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
     * Get questionid
     *
     * @return integer
     */
    public function getQuestionid()
    {
        return $this->questionid;
    }

    /**
     * Set question
     *
     * @param string $question
     *
     * @return Question
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set required
     *
     * @param boolean $required
     *
     * @return Question
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get required
     *
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set subcategorySubcategoryid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Subcategory $subcategorySubcategoryid
     *
     * @return Question
     */
    public function setSubcategorySubcategoryid(\UNO\EvaluacionesBundle\Entity\Subcategory $subcategorySubcategoryid = null)
    {
        $this->subcategorySubcategoryid = $subcategorySubcategoryid;

        return $this;
    }

    /**
     * Get subcategorySubcategoryid
     *
     * @return \UNO\EvaluacionesBundle\Entity\Subcategory
     */
    public function getSubcategorySubcategoryid()
    {
        return $this->subcategorySubcategoryid;
    }

    /**
     * Add surveySurveyid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Survey $surveySurveyid
     *
     * @return Question
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
