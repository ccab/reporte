<?php

namespace Reporte\MarcaBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * Marca
 *
 * @ORM\Table(name="marcas")
 * @ORM\Entity
 */
class Marca
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
     * @ORM\Column(name="nombre", type="string", length=50)
     */
    private $nombre;

    /**
    * @ORM\OneToMany(targetEntity="Reporte\PCBundle\Entity\PC", mappedBy="marca")
    */
    protected $pcs;
    
    
    public function __construct()
    {
        $this->pcs = new ArrayCollection();
    }


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
     * Set nombre
     *
     * @param string $nombre
     * @return Marca
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Add pcs
     *
     * @param \Reporte\PCBundle\Entity\PC $pcs
     * @return Marca
     */
    public function addPc(\Reporte\PCBundle\Entity\PC $pcs)
    {
        $this->pcs[] = $pcs;

        return $this;
    }

    /**
     * Remove pcs
     *
     * @param \Reporte\PCBundle\Entity\PC $pcs
     */
    public function removePc(\Reporte\PCBundle\Entity\PC $pcs)
    {
        $this->pcs->removeElement($pcs);
    }

    /**
     * Get pcs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPcs()
    {
        return $this->pcs;
    }
}
