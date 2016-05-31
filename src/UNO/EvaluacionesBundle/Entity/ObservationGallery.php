<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ObservationGallery
 *
 * @ORM\Table(name="ObservationGallery")
 * @ORM\Entity
 */
class ObservationGallery
{
    /**
     * @var string
     *
     * @ORM\Column(name="observationGalleryId", type="string", length=65)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $observationGalleryId;

    /**
     * @var string
     *
     * @ORM\Column(name="dir", type="string", length=255)
     */
    private $dir;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=1)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateUpload", type="datetime")
     */
    private $dateUpload;

    /**
     * @var integer
     *
     * @ORM\Column(name="observationId", type="integer")
     */
    private $observationId;

    /**
     * Set observationGalleryId
     *
     * @param string $observationGalleryId
     *
     * @return ObservationGallery
     */
    public function setObservationGalleryId($observationGalleryId)
    {
        $this->observationGalleryId = $observationGalleryId;

        return $this;
    }

    /**
     * Get observationGalleryId
     *
     * @return string
     */
    public function getObservationGalleryId()
    {
        return $this->observationGalleryId;
    }

    /**
     * Set dir
     *
     * @param string $dir
     *
     * @return ObservationGallery
     */
    public function setDir($dir)
    {
        $this->dir = $dir;

        return $this;
    }

    /**
     * Get dir
     *
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return ObservationGallery
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set dateUpload
     * @param \DateTime $dateUpload
     * @return ObservationGallery
     */
    public function setDateUpload($dateUpload)
    {
        $this->dateUpload = $dateUpload;

        return $this;
    }

    /**
     * Get dateUpload
     *
     * @return \DateTime
     */
    public function getDateUpload()
    {
        return $this->dateUpload;
    }

    /**
     * Set observationId
     * @param integer $observationId
     * @return ObservationGallery
     */
    public function setObservationId($observationId)
    {
        $this->observationId = $observationId;

        return $this;
    }

    /**
     * Get observationId
     *
     * @return integer
     */
    public function getObservationId()
    {
        return $this->observationId;
    }

}
