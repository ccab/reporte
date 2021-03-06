<?php

namespace Reporte\PCBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * PC
 *
 * @ORM\Table(name="pcs")
 * @ORM\Entity(repositoryClass="Reporte\PCBundle\Entity\PCRepository")
 */
class PC
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
     * @ORM\Column(name="inv", type="string", length=25, unique=true)
     * @Assert\NotBlank
     */
    private $inv;
    
    /**
    * @ORM\ManyToOne(targetEntity="Reporte\MarcaBundle\Entity\Marca", inversedBy="pcs")
    * @ORM\JoinColumn(name="marca_id", referencedColumnName="id")
    * @Assert\NotBlank
    */
    protected $marca;
    
    /**
    * @ORM\ManyToOne(targetEntity="Reporte\AreaBundle\Entity\Area", inversedBy="pcs")
    * @ORM\JoinColumn(name="area_id", referencedColumnName="id")
    * @Assert\NotBlank
    */
    protected $area;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="es_laptop", type="boolean")
    */
    protected $esLaptop;
    
    /**
    * @ORM\OneToMany(targetEntity="Reporte\ServicioBundle\Entity\Servicio", mappedBy="pc")
    */
    protected $servicios;
    
    
    public function __construct()
    {
        $this->servicios = new ArrayCollection();
        $this->esLaptop = false;
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
     * Set inv
     *
     * @param string $inv
     * @return PC
     */
    public function setInv($inv)
    {
        $this->inv = $inv;

        return $this;
    }

    /**
     * Get inv
     *
     * @return string 
     */
    public function getInv()
    {
        return $this->inv;
    }


    /**
     * Set area
     *
     * @param \Reporte\AreaBundle\Entity\Area $area
     * @return PC
     */
    public function setArea(\Reporte\AreaBundle\Entity\Area $area = null)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return \Reporte\AreaBundle\Entity\Area 
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set marca
     *
     * @param \Reporte\MarcaBundle\Entity\Marca $marca
     * @return PC
     */
    public function setMarca(\Reporte\MarcaBundle\Entity\Marca $marca = null)
    {
        $this->marca = $marca;

        return $this;
    }

    /**
     * Get marca
     *
     * @return \Reporte\MarcaBundle\Entity\Marca 
     */
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * Add servicios
     *
     * @param \Reporte\ServicioBundle\Entity\Servicio $servicios
     * @return PC
     */
    public function addServicio(\Reporte\ServicioBundle\Entity\Servicio $servicios)
    {
        $this->servicios[] = $servicios;

        return $this;
    }

    /**
     * Remove servicios
     *
     * @param \Reporte\ServicioBundle\Entity\Servicio $servicios
     */
    public function removeServicio(\Reporte\ServicioBundle\Entity\Servicio $servicios)
    {
        $this->servicios->removeElement($servicios);
    }

    /**
     * Get servicios
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getServicios()
    {
        return $this->servicios;
    }

    /**
     * Set esLaptop
     *
     * @param boolean $esLaptop
     * @return PC
     */
    public function setEsLaptop($esLaptop)
    {
        $this->esLaptop = $esLaptop;

        return $this;
    }

    /**
     * Get esLaptop
     *
     * @return boolean 
     */
    public function getEsLaptop()
    {
        return $this->esLaptop;
    }
}
