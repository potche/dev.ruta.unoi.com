<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Feature
 *
 * @ORM\Table(name="Feature")
 * @ORM\Entity
 */
class Feature
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idFeature", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idFeature;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="idVersion", type="integer", nullable=false)
     */
    private $idVersion;


    /**
     * Get idCversion
     *
     * @return integer
     */
    public function getIdCversion()
    {
        return $this->idCversion;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Feature
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
     * @param text $description
     *
     * @return Feature
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return text
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set idVersion
     *
     * @param integer $idVersion
     *
     * @return Feature
     */
    public function setIdVersion($idVersion)
    {
        $this->idVersion = $idVersion;

        return $this;
    }

    /**
     * Get idVersion
     *
     * @return integer
     */
    public function getIdVersion()
    {
        return $this->idVersion;
    }

}

