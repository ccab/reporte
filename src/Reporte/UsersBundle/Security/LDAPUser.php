<?php
namespace Reporte\UsersBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
/**
 * Represents a User Object
 * Based on Ztec\Security\ActiveDirectoryBundle\Security\User\User Class
 *
 * @author rafael
 */
class LDAPUser implements UserInterface, EquatableInterface
{
    private $username;
    private $password;
    private $salt;
    private $roles;
    private $fisrtName;
    private $fullName;
    private $email;

    public function __construct($username, $password, array $roles, $firstName, $fullName, $email)
    {
        $this->username = $username;
        $this->password = $password;
        $this->salt = null;
        $this->roles = $roles;
        $this->fisrtName = $firstName;
        $this->fullName = $fullName;
        $this->email = $email;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    public function getFisrtName() {
        return $this->fisrtName;
    }

    public function setFisrtName($fisrtName) {
        $this->fisrtName = $fisrtName;
    }

    
    public function getFullName() {
        return $this->fullName;
    }

    public function setFullName($fullName) {
        $this->fullName = $fullName;
    }

        
    /**
     * Returns the email address of the authenticated user.
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set the email address of the authenticated user.
     *
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     *
     * @return void
     */
    public function eraseCredentials()
    {
        //return void;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array Role[] The user roles
     */
    public function getRoles()
    {
        return  $this->roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @stof: Its goal is to decide whether the user must authenticate again 
     *        if the user serialized in the session does not match the user 
     *        retrieved from the DB. It generally makes sense to force 
     *        reauthenticated if the username or the hashed passwords are 
     *        different (i.e. they have been modified from another part of the system)
     * 
     * Here we didn't check for the password because we manually set it when we fetching
     * it from DC, so the retrieved user password (from DC) and 'this' user password 
     * (from the session) doesn't match.
     */
    public function isEqualTo(UserInterface $user) 
    {
        if (!$user instanceof LDAPUser) 
        {
            return false;
        }
        
        if ($this->username !== $user->getUsername()) 
        {
            return false;
        }
        
        return true;
    }

    
    
    
    
    
    
}

