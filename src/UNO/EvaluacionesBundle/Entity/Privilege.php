<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Privilege
 *
 * @ORM\Entity
 */
class Privilege
{
    /**
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $optionApplicationId;

    /**
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $profileId;

    /**
     * Set optionApplicationId
     *
     * @param integer $optionApplicationId
     *
     * @return Privilege
     */
    public function setOptionApplicationId($optionApplicationId)
    {
        $this->optionApplicationId = $optionApplicationId;

        return $this;
    }

    /**
     * Get optionApplicationId
     *
     * @return integer
     */
    public function getOptionApplicationId()
    {
        return $this->optionApplicationId;
    }

    /**
     * Set profileId
     *
     * @param integer $profileId
     *
     * @return Privilege
     */
    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;

        return $this;
    }

    /**
     * Get profileId
     *
     * @return integer
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

}
