<?php

namespace Reporte\UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Role
 *
 * @ORM\Table(name="roles_reporte")
 * @ORM\Entity
 */
class Role implements RoleInterface
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
     * @ORM\Column(name="name", type="string", length=50, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=50, unique=true)
     */
    private $role;
 
    /**
    * @ORM\ManyToMany(targetEntity="User", mappedBy="myRoles")
    */
    private $users;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nombre_grupo_dc", type="string", length=50)
     */
    private $nombre_grupo_dc;
    
    /////////////////////////////necesitado por RoleInterface/////////////////////////////////
    
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getRole()
    {
        return $this->role;
    }

    //////////////////////////////////////autogenerados//////////////////////////////////////////
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
     * Set name
     *
     * @param string $name
     * @return Role
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return Role
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }


    public function getNombreGrupoDC() {
        return $this->nombre_grupo_dc;
    }

    public function setNombreGrupoDC($nombre_grupo_dc) {
        $this->nombre_grupo_dc = $nombre_grupo_dc;
    }

    
    /**
     * Add users
     *
     * @param \Reporte\UserBundle\Entity\User $users
     * @return Role
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
