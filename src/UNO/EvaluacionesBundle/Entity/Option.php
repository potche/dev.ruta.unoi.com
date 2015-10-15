<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Option
 *
 * @ORM\Table(name="`Option`")
 * @ORM\Entity
 */
class Option
{
    /**
     * @var string
     *
     *
     * @ORM\Column(name="`option`", type="string", length=250, nullable=false)
     */
    private $option;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=30, nullable=false)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="optionId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $optionid;



    /**
     * Set option
     *
     * @param string $option
     *
     * @return Option
     */
    public function setOption($option)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * Get option
     *
     * @return string
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Option
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
     * Get optionid
     *
     * @return integer
     */
    public function getOptionid()
    {
        return $this->optionid;
    }
}
