<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ObservationDisposition
 *
 * @ORM\Table(name="ObservationDisposition")
 * @ORM\Entity
 */
class ObservationDisposition
{
    /**
     * @var integer
     *
     * @ORM\Column(name="observationId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $observationId;

    /**
     * @var string
     *
     * @ORM\Column(name="disposition", type="string", length="45")
     */
    private $disposition;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDisposition", type="datetime")
     */
    private $dateDisposition;

    /**
     * Get observationId
     * @return integer
     */
    public function getObservationId()
    {
        return $this->observationId;
    }

    /**
     * Set observationId
     * @param integer $observationId
     * @return ObservationDisposition
     */
    public function setObservationId($observationId)
    {
        $this->observationId = $observationId;
        return $this;
    }

    /**
     * Get disposition
     * @return string
     */
    public function getDisposition()
    {
        return $this->disposition;
    }

    /**
     * Set disposition
     * @param string $disposition
     * @return ObservationDisposition
     */
    public function setDisposition($disposition)
    {
        $this->disposition = $disposition;
        return $this;
    }

    /**
     * Set dateDisposition
     * @param \DateTime $dateDisposition
     * @return ObservationDisposition
     */
    public function setDateDisposition($dateDisposition)
    {
        $this->dateDisposition = $dateDisposition;

        return $this;
    }

    /**
     * Get dateDisposition
     *
     * @return \DateTime
     */
    public function getDateDisposition()
    {
        return $this->dateDisposition;
    }
}

