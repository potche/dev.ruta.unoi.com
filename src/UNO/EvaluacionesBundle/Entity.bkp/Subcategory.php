<?php

namespace UNO\EvaluacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subcategory
 *
 * @ORM\Table(name="Subcategory", indexes={@ORM\Index(name="fk_Subcategoria_Categoria1_idx", columns={"Category_categoryId"})})
 * @ORM\Entity
 */
class Subcategory
{
    /**
     * @var string
     *
     * @ORM\Column(name="subcategory", type="string", length=250, nullable=false)
     */
    private $subcategory;

    /**
     * @var integer
     *
     * @ORM\Column(name="subcategoryId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $subcategoryid;

    /**
     * @var \UNO\EvaluacionesBundle\Entity\Category
     *
     * @ORM\ManyToOne(targetEntity="UNO\EvaluacionesBundle\Entity\Category")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Category_categoryId", referencedColumnName="categoryId")
     * })
     */
    private $categoryCategoryid;



    /**
     * Set subcategory
     *
     * @param string $subcategory
     *
     * @return Subcategory
     */
    public function setSubcategory($subcategory)
    {
        $this->subcategory = $subcategory;

        return $this;
    }

    /**
     * Get subcategory
     *
     * @return string
     */
    public function getSubcategory()
    {
        return $this->subcategory;
    }

    /**
     * Get subcategoryid
     *
     * @return integer
     */
    public function getSubcategoryid()
    {
        return $this->subcategoryid;
    }

    /**
     * Set categoryCategoryid
     *
     * @param \UNO\EvaluacionesBundle\Entity\Category $categoryCategoryid
     *
     * @return Subcategory
     */
    public function setCategoryCategoryid(\UNO\EvaluacionesBundle\Entity\Category $categoryCategoryid = null)
    {
        $this->categoryCategoryid = $categoryCategoryid;

        return $this;
    }

    /**
     * Get categoryCategoryid
     *
     * @return \UNO\EvaluacionesBundle\Entity\Category
     */
    public function getCategoryCategoryid()
    {
        return $this->categoryCategoryid;
    }
}
