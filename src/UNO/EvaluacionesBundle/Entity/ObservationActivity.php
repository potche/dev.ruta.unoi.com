<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ObservationActivity
 *
 * @ORM\Table(name="ObservationActivity")
 * @ORM\Entity
 */
class ObservationActivity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="observationActivityId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $observationActivityId;

    /**
     * @var integer
     *
     * @ORM\Column(name="order", type="integer")
     */
    private $order;

    /**
     * @var string
     *
     * @ORM\Column(name="activity", type="string", length=255)
     */
    private $activity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startActivity", type="datetime")
     */
    private $startActivity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endActivity", type="datetime")
     */
    private $endActivity;

    /**
     * @var integer
     *
     * @ORM\Column(name="observationId", type="integer")
     */
    private $observationId;

    /**
     * Get observationActivityId
     *
     * @return integer
     */
    public function getObservationActivityId()
    {
        return $this->observationActivityId;
    }

    /**
     * Set order
     *
     * @param integer $order
     *
     * @return ObservationActivity
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
     * Set activity
     *
     * @param string $activity
     *
     * @return ObservationActivity
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * Get activity
     *
     * @return string
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * Set startActivity
     * @param \DateTime $startActivity
     * @return ObservationActivity
     */
    public function setStartActivity($startActivity)
    {
        $this->startActivity = $startActivity;

        return $this;
    }

    /**
     * Get startActivity
     *
     * @return \DateTime
     */
    public function getStartActivity()
    {
        return $this->startActivity;
    }

    /**
     * Set endActivity
     * @param \DateTime $endActivity
     * @return ObservationActivity
     */
    public function setEndActivity($endActivity)
    {
        $this->endActivity = $endActivity;

        return $this;
    }

    /**
     * Get endActivity
     *
     * @return \DateTime
     */
    public function getEndActivity()
    {
        return $this->endActivity;
    }

    /**
     * Set observationId
     * @param integer $observationId
     * @return ObservationActivity
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
