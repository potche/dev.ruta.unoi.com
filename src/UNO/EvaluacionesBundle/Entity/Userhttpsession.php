<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Userhttpsession
 *
 * @ORM\Table(name="UserHttpSession", indexes={@ORM\Index(name="fk_UserHttpSession_Person1_idx", columns={"personId"})})
 * @ORM\Entity
 */
class Userhttpsession
{
    /**
     * @var string
     *
     * @ORM\Column(name="idSession", type="string", length=255, nullable=false)
     */
    private $idsession;

    /**
     * @var string
     *
     * @ORM\Column(name="browser", type="string", length=25, nullable=false)
     */
    private $browser;

    /**
     * @var string
     *
     * @ORM\Column(name="browserVersion", type="string", length=25, nullable=false)
     */
    private $browserversion;

    /**
     * @var string
     *
     * @ORM\Column(name="platform", type="string", length=25, nullable=false)
     */
    private $platform;

    /**
     * @var string
     *
     * @ORM\Column(name="sid", type="string", length=65, nullable=false)
     */
    private $sid;

    /**
     * @var string
     *
     * @ORM\Column(name="ipCliente", type="string", length=45, nullable=false)
     */
    private $ipcliente;

    /**
     * @var boolean
     *
     * @ORM\Column(name="loggedIn", type="boolean", nullable=false)
     */
    private $loggedin = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text", length=65535, nullable=false)
     */
    private $data;

    /**
     * @var integer
     *
     * @ORM\Column(name="idUserHttpSession", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $iduserhttpsession;

    /**
     * @var \UNO\EvaluacionesBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="UNO\EvaluacionesBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="personId", referencedColumnName="personId")
     * })
     */
    private $personid;



    /**
     * Set idsession
     *
     * @param string $idsession
     *
     * @return Userhttpsession
     */
    public function setIdsession($idsession)
    {
        $this->idsession = $idsession;

        return $this;
    }

    /**
     * Get idsession
     *
     * @return string
     */
    public function getIdsession()
    {
        return $this->idsession;
    }

    /**
     * Set browser
     *
     * @param string $browser
     *
     * @return Userhttpsession
     */
    public function setBrowser($browser)
    {
        $this->browser = $browser;

        return $this;
    }

    /**
     * Get browser
     *
     * @return string
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * Set browserversion
     *
     * @param string $browserversion
     *
     * @return Userhttpsession
     */
    public function setBrowserversion($browserversion)
    {
        $this->browserversion = $browserversion;

        return $this;
    }

    /**
     * Get browserversion
     *
     * @return string
     */
    public function getBrowserversion()
    {
        return $this->browserversion;
    }

    /**
     * Set platform
     *
     * @param string $platform
     *
     * @return Userhttpsession
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get platform
     *
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Set sid
     *
     * @param string $sid
     *
     * @return Userhttpsession
     */
    public function setSid($sid)
    {
        $this->sid = $sid;

        return $this;
    }

    /**
     * Get sid
     *
     * @return string
     */
    public function getSid()
    {
        return $this->sid;
    }

    /**
     * Set ipcliente
     *
     * @param string $ipcliente
     *
     * @return Userhttpsession
     */
    public function setIpcliente($ipcliente)
    {
        $this->ipcliente = $ipcliente;

        return $this;
    }

    /**
     * Get ipcliente
     *
     * @return string
     */
    public function getIpcliente()
    {
        return $this->ipcliente;
    }

    /**
     * Set loggedin
     *
     * @param boolean $loggedin
     *
     * @return Userhttpsession
     */
    public function setLoggedin($loggedin)
    {
        $this->loggedin = $loggedin;

        return $this;
    }

    /**
     * Get loggedin
     *
     * @return boolean
     */
    public function getLoggedin()
    {
        return $this->loggedin;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return Userhttpsession
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get iduserhttpsession
     *
     * @return integer
     */
    public function getIduserhttpsession()
    {
        return $this->iduserhttpsession;
    }

    /**
     * Set personid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Person $personid
     *
     * @return Userhttpsession
     */
    public function setPersonid(\UNO\EvaluacionesBundle\Entity\Person $personid = null)
    {
        $this->personid = $personid;

        return $this;
    }

    /**
     * Get personid
     *
     * @return \UNO\EvaluacionesBundle\Entity\Person
     */
    public function getPersonid()
    {
        return $this->personid;
    }
}
