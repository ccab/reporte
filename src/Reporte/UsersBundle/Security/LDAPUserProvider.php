<?php
namespace Reporte\UsersBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use Reporte\UsersBundle\Security\LDAPUser;
use Reporte\UsersBundle\Security\LDAPService;

/**
 * Based on: How to create a custom User Provider
 *
 * @author rafael
 */
class LDAPUserProvider implements UserProviderInterface
{
    private $ldap;
    
    public function __construct(LDAPService $ldap)
    {
        $this->ldap = $ldap;
    }
    
    public function loadUserByUsername($username) 
    {
        //we can use the ldap service to find a AD user
        $userData = $this->ldap->find($username);
        
        //it returns an object on success, false if there is no user
        if ($userData) 
        {
            $userGroups = $this->ldap->groups($username);
            
            //the password must be set statically due to AD doesn't return neither the salt nor hashed password
            return new LDAPUser($userData->samAccountName, "notRealPasswd", $userGroups, $userData->givenName, $userData->name, $userData->userPrincipalName);
        }
        
        throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));

    }

    public function refreshUser(UserInterface $user) 
    {
        if (!$user instanceof LDAPUser)
        {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class) 
    {
        return $class === 'Reporte\UsersBundle\Security\LDAPUser';
    }    
}
