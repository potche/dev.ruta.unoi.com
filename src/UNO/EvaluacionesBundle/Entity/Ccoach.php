<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ccoach
 *
 * @ORM\Table(name="Ccoach")
 * @ORM\Entity
 */
class Ccoach
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=255)
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="coordinador", type="integer")
     */
    private $coordinador;

    /**
     * @var string
     *
     * @ORM\Column(name="zona", type="string", length=25)
     */
    private $zona;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return Ccoach
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set coordinador
     *
     * @param integer $coordinador
     *
     * @return Ccoach
     */
    public function setCoordinador($coordinador)
    {
        $this->coordinador = $coordinador;

        return $this;
    }

    /**
     * Get coordinador
     *
     * @return integer
     */
    public function getCoordinador()
    {
        return $this->user;
    }

    /**
     * Set zona
     *
     * @param string $zona
     *
     * @return Ccoach
     */
    public function setZona($zona)
    {
        $this->zona = $zona;

        return $this;
    }

    /**
     * Get zona
     *
     * @return string
     */
    public function getZona()
    {
        return $this->user;
    }
}

