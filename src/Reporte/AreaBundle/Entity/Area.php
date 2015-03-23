<?php

namespace Reporte\AreaBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * Area
 *
 * @ORM\Table(name="areas")
 * @ORM\Entity
 */
class Area
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
     * @var string
     *
     * @ORM\Column(name="nombre_grupo_dc", type="string", length=50)
     */
    private $nombre_grupo_dc;

    /**
    * @ORM\OneToMany(targetEntity="Reporte\PCBundle\Entity\PC", mappedBy="area")
    */
    protected $pcs;
    
    /**
    * @ORM\OneToMany(targetEntity="Reporte\UserBundle\Entity\User", mappedBy="area")
    */
    protected $users;
    
    
    public function __construct()
    {
        $this->pcs = new ArrayCollection();
        $this->users = new ArrayCollection();
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
     * @return Area
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
    
    
    public function getNombreGrupoDC() {
        return $this->nombre_grupo_dc;
    }

    public function setNombreGrupoDC($nombre_grupo_dc) {
        $this->nombre_grupo_dc = $nombre_grupo_dc;
    }

    
    /**
     * Add pcs
     *
     * @param \Reporte\PCBundle\Entity\PC $pcs
     * @return Area
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

    /**
     * Add users
     *
     * @param \Reporte\UserBundle\Entity\User $users
     * @return Area
     */
    public function addUser(\Reporte\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Reporte\UserBundle\Entity\User $users
     */
    public function removeUser(\Reporte\UserBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }
}
