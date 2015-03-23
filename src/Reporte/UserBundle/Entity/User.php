<?php

namespace Reporte\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User
 *
 * @ORM\Table(name="usuarios")
 * @ORM\Entity(repositoryClass="Reporte\UserBundle\Entity\UserRepository")
 * 
 */
class User implements UserInterface, \Serializable
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
     * @ORM\Column(name="username", type="string", length=50, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50, unique=true)
     */
    private $email;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
    * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
    *
    */
    private $roles;
    
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
    * @ORM\OneToMany(targetEntity="Reporte\ServicioBundle\Entity\Servicio", mappedBy="tecnico")
    */
    protected $servicios_solucionados;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="login_time", type="datetime", nullable=true)
     */
    private $login_time;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_access", type="datetime", nullable=true)
     */
    private $last_access;
    
    public function __construct()
    {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->roles = new ArrayCollection();
        $this->servicios = new ArrayCollection();
        $this->servicios_solucionados = new ArrayCollection();
        $this->esTecnico = false;
    }

    
    /////////////////////////////necesitados por UserInterface///////////////////////
    
    public function getUsername()
    {
        return $this->username;
    }
    
    public function getSalt()
    {
        return $this->salt;
    }
    
    public function getPassword()
    {
        return $this->password;
    }
    
    
    public function getRoles()
    {
        return $this->roles->toArray();
        //return array('ROLE_USER');
    }
    
    public function eraseCredentials(){}

    ////////////////////////////////necesitados por \Serializable////////////////////////////////
    
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->salt,
            $this->password,
            ));
    }
    
    
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->salt,
            $this->password,
        ) = unserialize($serialized);
    }
    
    ////////////////////////////////generados/////////////////////////////////////////

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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

  

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }



    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }



    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Add roles
     *
     * @param \Reporte\UserBundle\Entity\Role $roles
     * @return User
     */
    public function addRole(\Reporte\UserBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \Reporte\UserBundle\Entity\Role $roles
     */
    public function removeRole(\Reporte\UserBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Set area
     *
     * @param \Reporte\AreaBundle\Entity\Area $area
     * @return User
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

    /**
     * Add servicios_solucionados
     *
     * @param \Reporte\ServicioBundle\Entity\Servicio $serviciosSolucionados
     * @return User
     */
    public function addServiciosSolucionado(\Reporte\ServicioBundle\Entity\Servicio $serviciosSolucionados)
    {
        $this->servicios_solucionados[] = $serviciosSolucionados;

        return $this;
    }

    /**
     * Remove servicios_solucionados
     *
     * @param \Reporte\ServicioBundle\Entity\Servicio $serviciosSolucionados
     */
    public function removeServiciosSolucionado(\Reporte\ServicioBundle\Entity\Servicio $serviciosSolucionados)
    {
        $this->servicios_solucionados->removeElement($serviciosSolucionados);
    }

    /**
     * Get servicios_solucionados
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getServiciosSolucionados()
    {
        return $this->servicios_solucionados;
    }

    /**
     * Set login_time
     *
     * @param \DateTime $loginTime
     * @return User
     */
    public function setLoginTime($loginTime)
    {
        $this->login_time = $loginTime;

        return $this;
    }

    /**
     * Get login_time
     *
     * @return \DateTime 
     */
    public function getLoginTime()
    {
        return $this->login_time;
    }


    /**
     * Set last_access
     *
     * @param \DateTime $lastAccess
     * @return User
     */
    public function setLastAccess($lastAccess)
    {
        $this->last_access = $lastAccess;

        return $this;
    }

    /**
     * Get last_access
     *
     * @return \DateTime 
     */
    public function getLastAccess()
    {
        return $this->last_access;
    }
}
