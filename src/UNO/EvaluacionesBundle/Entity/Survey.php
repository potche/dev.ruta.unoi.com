<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Survey
 *
 * @ORM\Table(name="Survey", uniqueConstraints={@ORM\UniqueConstraint(name="idEncuesta_UNIQUE", columns={"surveyId"})})
 * @ORM\Entity
 */
class Survey
{
    /**
     * @var integer
     *
     * @ORM\Column(name="surveyId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $surveyid;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=250, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=250, nullable=false)
     */
    private $title = 'Sin título';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=300, nullable=true)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = '1';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="closingDate", type="datetime", nullable=false)
     */
    private $closingdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modifiedDate", type="datetime", nullable=false)
     */
    private $modifieddate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationDate", type="datetime", nullable=false)
     */
    private $creationdate;

    /**
     * @var string
     *
     * @ORM\Column(name="createdBy", type="string", length=250, nullable=false)
     */
    private $createdby;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="UNO\EvaluacionesBundle\Entity\Question", inversedBy="surveySurveyid")
     * @ORM\JoinTable(name="questionxsurvey",
     *   joinColumns={
     *     @ORM\JoinColumn(name="Survey_surveyId", referencedColumnName="surveyId")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="Question_questionId", referencedColumnName="questionId")
     *   }
     * )
     */
    private $questionQuestionid;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="UNO\EvaluacionesBundle\Entity\Profile", mappedBy="surveySurveyid")
     */
    private $profileProfileid;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->questionQuestionid = new \Doctrine\Common\Collections\ArrayCollection();
        $this->profileProfileid = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get surveyid
     *
     * @return integer
     */
    public function getSurveyid()
    {
        return $this->surveyid;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Survey
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Survey
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Survey
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Survey
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set closingdate
     *
     * @param \DateTime $closingdate
     *
     * @return Survey
     */
    public function setClosingdate($closingdate)
    {
        $this->closingdate = $closingdate;

        return $this;
    }

    /**
     * Get closingdate
     *
     * @return \DateTime
     */
    public function getClosingdate()
    {
        return $this->closingdate;
    }

    /**
     * Set modifieddate
     *
     * @param \DateTime $modifieddate
     *
     * @return Survey
     */
    public function setModifieddate($modifieddate)
    {
        $this->modifieddate = $modifieddate;

        return $this;
    }

    /**
     * Get modifieddate
     *
     * @return \DateTime
     */
    public function getModifieddate()
    {
        return $this->modifieddate;
    }

    /**
     * Set creationdate
     *
     * @param \DateTime $creationdate
     *
     * @return Survey
     */
    public function setCreationdate($creationdate)
    {
        $this->creationdate = $creationdate;

        return $this;
    }

    /**
     * Get creationdate
     *
     * @return \DateTime
     */
    public function getCreationdate()
    {
        return $this->creationdate;
    }

    /**
     * Set createdby
     *
     * @param string $createdby
     *
     * @return Survey
     */
    public function setCreatedby($createdby)
    {
        $this->createdby = $createdby;

        return $this;
    }

    /**
     * Get createdby
     *
     * @return string
     */
    public function getCreatedby()
    {
        return $this->createdby;
    }

    /**
     * Add questionQuestionid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Question $questionQuestionid
     *
     * @return Survey
     */
    public function addQuestionQuestionid(\UNO\EvaluacionesBundle\Entity\Question $questionQuestionid)
    {
        $this->questionQuestionid[] = $questionQuestionid;

        return $this;
    }

    /**
     * Remove questionQuestionid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Question $questionQuestionid
     */
    public function removeQuestionQuestionid(\UNO\EvaluacionesBundle\Entity\Question $questionQuestionid)
    {
        $this->questionQuestionid->removeElement($questionQuestionid);
    }

    /**
     * Get questionQuestionid
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestionQuestionid()
    {
        return $this->questionQuestionid;
    }

    /**
     * Add profileProfileid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Profile $profileProfileid
     *
     * @return Survey
     */
    public function addProfileProfileid(\UNO\EvaluacionesBundle\Entity\Profile $profileProfileid)
    {
        $this->profileProfileid[] = $profileProfileid;

        return $this;
    }

    /**
     * Remove profileProfileid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Profile $profileProfileid
     */
    public function removeProfileProfileid(\UNO\EvaluacionesBundle\Entity\Profile $profileProfileid)
    {
        $this->profileProfileid->removeElement($profileProfileid);
    }

    /**
     * Get profileProfileid
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProfileProfileid()
    {
        return $this->profileProfileid;
    }
}
