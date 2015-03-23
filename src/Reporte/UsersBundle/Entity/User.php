<?php

namespace Reporte\UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Reporte\UsersBundle\Entity\Role;

/**
 * User
 *
 * @ORM\Table(name="usuarios_reporte")
 * @ORM\Entity(repositoryClass="Reporte\UsersBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="first_name", type="string", length=50)
     */
    private $firstName;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="full_name", type="string", length=50)
     */
    private $fullName;
    
    /**
     * Logging the last time the user access to the website(the previous visit)
     * 
     * @var \DateTime
     *
     * @ORM\Column(name="last_access", type="datetime", nullable=true)
     */
    protected $lastAccess;
    
    /**
    * @ORM\ManyToOne(targetEntity="Reporte\AreaBundle\Entity\Area", inversedBy="users")
    * @ORM\JoinColumn(name="area_id", referencedColumnName="id")
    */
    protected $area;
    
    /**
    * @ORM\OneToMany(targetEntity="Reporte\ServicioBundle\Entity\Servicio", mappedBy="autor")
    */
    protected $servicios;
    
    /**
    * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
    * @ORM\JoinTable(name="usuarios_roles")
    * 
    * With this new property we can tweak the Doctrine mapping restriction to the role 
    * property itself, we override the access methods so we work with role entity instead 
    * of an array
    */
    protected $myRoles;

    public function __construct()
    {
        parent::__construct();
        $this->servicios = new ArrayCollection();
        $this->myRoles = new ArrayCollection();
        $this->enabled = true;
    }
 
    
    ////////////////////////////////roles//////////////////////////////////
    
    /**
     * Returns the user roles
     *
     * @return array The roles
     */
    public function getRoles()
    {
        return $this->myRoles->toArray();
    }
    
    
    
    public function setRoles(array $roles)
    {   
        $this->myRoles = new ArrayCollection();
        
        foreach ($roles as $role) 
        {
            $this->addRole($role);
        }
    }
    
    
    /**
     * Remove roles
     *
     * @param Role $role
     */
    public function removeRole($role)
    {
        $this->myRoles->removeElement($role);
    }
    
    
    /**
     * Add roles
     *
     * @param Role $role
     * @return User
     */
    public function addRole($role)
    {
        $this->myRoles[] = $role;
    }
    
    public function getMyRoles() 
    {
        return $this->myRoles;
    }

    public function setMyRoles($myRoles) 
    {
        $this->myRoles = $myRoles;
    }

        ////////////////////////////////roles//////////////////////////////////
    
    public function getFirstName() {
        return $this->firstName;
    }

    public function getFullName() {
        return $this->fullName;
    }

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    public function setFullName($fullName) {
        $this->fullName = $fullName;
    }
    
    public function getArea() {
        return $this->area;
    }

    public function setArea($area) {
        $this->area = $area;
    }

        
    public function getLastAccess() {
        return $this->lastAccess;
    }

    public function setLastAccess($lastAccess) {
        $this->lastAccess = $lastAccess;
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
     * Add servicios
     *
     * @param \Reporte\ServicioBundle\Entity\Servicio $servicios
     * @return User
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
}
