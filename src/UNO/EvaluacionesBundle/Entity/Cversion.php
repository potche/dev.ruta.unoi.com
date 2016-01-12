<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cversion
 *
 * @ORM\Table(name="Cversion")
 * @ORM\Entity
 */
class Cversion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idCversion", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idCversion;

    /**
     * @var string
     *
     * @ORM\Column(name="version", type="string", length=45)
     */
    private $version;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="releaseDate", type="datetime", nullable=false)
     */
    private $releaseDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="currentVersion", type="boolean", nullable=false)
     */
    private $currentVersion;


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
     * Set version
     *
     * @param string $version
     *
     * @return Cversion
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set releaseDate
     *
     * @param \DateTime $releaseDate
     *
     * @return Cversion
     */
    public function setReleaseDate($releaseDate)
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * Get releaseDate
     *
     * @return \DateTime
     */
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    /**
     * Set currentVersion
     *
     * @param boolean $currentVersion
     *
     * @return Cversion
     */
    public function setCurrentVersion($currentVersion)
    {
        $this->currentVersion = $currentVersion;

        return $this;
    }

    /**
     * Get currentVersion
     *
     * @return boolean
     */
    public function getCurrentVersion()
    {
        return $this->currentVersion;
    }

}

