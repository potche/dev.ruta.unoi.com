<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Person
 *
 * @ORM\Table(name="CodigoPostal")
 * @ORM\Entity
 */

class CodigoPostal
{
    /**
     * @var integer
     * @ORM\codigoPostalId
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="codigoPostalId", type="integer")
     */

    private $codigoPostalId;

    /**
     * @var string
     *
     * @ORM\Column(name="d_codigo", type="string", length=5, nullable=false)
     */
    private $d_codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="d_asenta", type="string", length=65, nullable=false)
     */
    private $d_asenta;

    /**
     * @var string
     *
     * @ORM\Column(name="d_tipo_asenta", type="string", length=45)
     */
    private $d_tipo_asenta;

    /**
     * @var string
     *
     * @ORM\Column(name="D_mnpio", type="string", length=65, nullable=false)
     */
    private $D_mnpio;

    /**
     * @var string
     *
     * @ORM\Column(name="d_estado", type="string", length=45, nullable=false)
     */
    private $d_estado;

    /**
     * @var string
     *
     * @ORM\Column(name="d_ciudad", type="string", length=45)
     */
    private $d_ciudad;

    /**
     * @var string
     *
     * @ORM\Column(name="d_CP", type="string", length=5)
     */
    private $d_CP;

    /**
     * @var string
     *
     * @ORM\Column(name="c_estado", type="string", length=2)
     */
    private $c_estado;

    /**
     * @var string
     *
     * @ORM\Column(name="c_oficina", type="string", length=5)
     */
    private $c_oficina;

    /**
     * @var integer
     *
     * @ORM\Column(name="c_CP", type="string", length=5)
     */
    private $c_CP;

    /**
     * @var string
     *
     * @ORM\Column(name="c_tipo_asenta", type="string", length=2)
     */
    private $c_tipo_asenta;

    /**
     * @var string
     *
     * @ORM\Column(name="c_mnpio", type="string", length=3)
     */
    private $c_mnpio;

    /**
     * @var string
     *
     * @ORM\Column(name="id_asenta_cpcons", type="string", length=5)
     */
    private $id_asenta_cpcons;

    /**
     * @var string
     *
     * @ORM\Column(name="d_zona", type="string", length=45)
     */
    private $d_zona;

    /**
     * @var string
     *
     * @ORM\Column(name="c_cve_ciudad", type="string", length=2)
     */
    private $c_cve_ciudad;

    /**
     * Get codigoPostalId
     *
     * @return integer
     */
    public function getCodigoPostalId()
    {
        return $this->codigoPostalId;
    }

    /**
     * Set d_codigo
     *
     * @param string $d_codigo
     *
     * @return CodigoPostal
     */
    public function setD_codigo($d_codigo)
    {
        $this->d_codigo = $d_codigo;

        return $this;
    }

    /**
     * Get d_codigo
     *
     * @return string
     */
    public function getD_codigo()
    {
        return $this->d_codigo;
    }

    /**
     * Set d_asenta
     *
     * @param string $d_asenta
     *
     * @return CodigoPostal
     */
    public function setD_asenta($d_asenta)
    {
        $this->d_asenta = $d_asenta;

        return $this;
    }

    /**
     * Get d_asenta
     *
     * @return string
     */
    public function getD_asenta()
    {
        return $this->d_asenta;
    }

    /**
     * Set d_tipo_asenta
     *
     * @param string $d_tipo_asenta
     *
     * @return CodigoPostal
     */
    public function setD_tipo_asenta($d_tipo_asenta)
    {
        $this->d_tipo_asenta = $d_tipo_asenta;

        return $this;
    }

    /**
     * Get d_tipo_asenta
     *
     * @return string
     */
    public function getD_tipo_asenta()
    {
        return $this->d_tipo_asenta;
    }

    /**
     * Set D_mnpio
     *
     * @param string $D_mnpio
     *
     * @return CodigoPostal
     */
    public function setD_mnpio($D_mnpio)
    {
        $this->D_mnpio = $D_mnpio;

        return $this;
    }

    /**
     * Get D_mnpio
     *
     * @return string
     */
    public function getD_mnpio()
    {
        return $this->D_mnpio;
    }

    /**
     * Set d_estado
     *
     * @param string $d_estado
     *
     * @return CodigoPostal
     */
    public function setD_estado($d_estado)
    {
        $this->d_estado = $d_estado;

        return $this;
    }

    /**
     * Get d_estado
     *
     * @return string
     */
    public function getD_estado()
    {
        return $this->d_estado;
    }

    /**
     * Set d_ciudad
     *
     * @param string $d_ciudad
     *
     * @return CodigoPostal
     */
    public function setD_ciudad($d_ciudad)
    {
        $this->d_ciudad = $d_ciudad;

        return $this;
    }

    /**
     * Get d_ciudad
     *
     * @return string
     */
    public function getD_ciudad()
    {
        return $this->d_ciudad;
    }

    /**
     * Set d_CP
     *
     * @param string $d_CP
     *
     * @return CodigoPostal
     */
    public function setD_CP($d_CP)
    {
        $this->d_CP = $d_CP;

        return $this;
    }

    /**
     * Get d_CP
     *
     * @return string
     */
    public function getD_CP()
    {
        return $this->d_CP;
    }

    /**
     * Set c_estado
     *
     * @param string $c_estado
     *
     * @return CodigoPostal
     */
    public function setC_estado($c_estado)
    {
        $this->c_estado = $c_estado;

        return $this;
    }

    /**
     * Get c_estado
     *
     * @return string
     */
    public function getC_estado()
    {
        return $this->c_estado;
    }

    /**
     * Set c_oficina
     *
     * @param string $c_oficina
     *
     * @return CodigoPostal
     */
    public function setC_oficina($c_oficina)
    {
        $this->c_oficina = $c_oficina;

        return $this;
    }

    /**
     * Get c_oficina
     *
     * @return string
     */
    public function getC_oficina()
    {
        return $this->c_oficina;
    }

    /**
     * Set c_CP
     *
     * @param string $c_CP
     *
     * @return CodigoPostal
     */
    public function setC_CP($c_CP)
    {
        $this->c_CP = $c_CP;

        return $this;
    }

    /**
     * Get c_CP
     *
     * @return integer
     */
    public function getC_CP()
    {
        return $this->c_CP;
    }

    /**
     * Set c_tipo_asenta
     *
     * @param string $c_tipo_asenta
     *
     * @return CodigoPostal
     */
    public function setC_tipo_asenta($c_tipo_asenta)
    {
        $this->c_tipo_asenta = $c_tipo_asenta;

        return $this;
    }

    /**
     * Get c_tipo_asenta
     *
     * @return string
     */
    public function getC_tipo_asenta()
    {
        return $this->c_tipo_asenta;
    }

    /**
     * Set c_mnpio
     *
     * @param string $c_mnpio
     *
     * @return CodigoPostal
     */
    public function setC_mnpio($c_mnpio)
    {
        $this->c_mnpio = $c_mnpio;

        return $this;
    }

    /**
     * Get c_mnpio
     *
     * @return string
     */
    public function getC_mnpio()
    {
        return $this->c_mnpio;
    }

    /**
     * Set id_asenta_cpcons
     *
     * @param string $id_asenta_cpcons
     *
     * @return CodigoPostal
     */
    public function setId_asenta_cpcons($id_asenta_cpcons)
    {
        $this->id_asenta_cpcons = $id_asenta_cpcons;

        return $this;
    }

    /**
     * Get id_asenta_cpcons
     *
     * @return string
     */
    public function getId_asenta_cpcons()
    {
        return $this->id_asenta_cpcons;
    }

    /**
     * Set d_zona
     *
     * @param string $d_zona
     *
     * @return CodigoPostal
     */
    public function setD_zona($d_zona)
    {
        $this->d_zona = $d_zona;

        return $this;
    }

    /**
     * Get d_zona
     *
     * @return string
     */
    public function getD_zona()
    {
        return $this->d_zona;
    }

    /**
     * Set c_cve_ciudad
     *
     * @param string $c_cve_ciudad
     *
     * @return CodigoPostal
     */
    public function setC_cve_ciudad($c_cve_ciudad)
    {
        $this->c_cve_ciudad = $c_cve_ciudad;

        return $this;
    }

    /**
     * Get c_cve_ciudad
     *
     * @return string
     */
    public function getC_cve_ciudad()
    {
        return $this->c_cve_ciudad;
    }

}
