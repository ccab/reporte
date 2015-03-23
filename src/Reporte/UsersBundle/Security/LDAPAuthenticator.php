<?php
namespace Reporte\UsersBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\ORM\EntityManager;

use Reporte\UsersBundle\Entity\User;
use Reporte\UsersBundle\Security\LDAPService;
use Reporte\UsersBundle\Security\LDAPUser;

/**
 * Based on: How to Create a Custom Form Password Authenticator
 *
 * @author rafael
 */
class LDAPAuthenticator implements SimpleFormAuthenticatorInterface
{
    private $encoderFactory;
    private $ldap;
    private $em;

    public function __construct(EncoderFactoryInterface $encoderFactory, LDAPService $ldap, EntityManager $em)
    {
        $this->encoderFactory = $encoderFactory;
        $this->ldap = $ldap;
        $this->em = $em;
    }
    
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey) 
    {
        try 
        {
            $user = $userProvider->loadUserByUsername($token->getUsername());
        } 
        catch (UsernameNotFoundException $e) 
        {
            throw new AuthenticationException($e->getMessage());
        }
        
        if ($user instanceof LDAPUser)
        {
            $user = $this->LDAP2DB($user, $token);
        }
        
        $encoder = $this->encoderFactory->getEncoder($user);
        $passwordValid = $encoder->isPasswordValid($user->getPassword(),$token->getCredentials(),$user->getSalt());

        if ($passwordValid) 
        {
            return new UsernamePasswordToken($user,$user->getPassword(),$providerKey,$user->getRoles());
        }
        
        throw new AuthenticationException('Invalid username or password');
        
    }

    public function createToken(Request $request, $username, $password, $providerKey) 
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }

    public function supportsToken(TokenInterface $token, $providerKey) 
    {
        return $token instanceof UsernamePasswordToken && $token->getProviderKey() === $providerKey;
    }    
    
    /**
     * Help function to sync the dc user with the local db user
     */
    private function LDAP2DB(LDAPUser $user, TokenInterface $token)
    {
        $ldapAuth = $this->ldap->authenticate($user->getUsername(), $token->getCredentials());

        //if the user authenticate against DC 
        if ($ldapAuth)
        {
            $fosUser = $this->em->getRepository('ReporteUsersBundle:User')->findOneBy(array('username' => $user->getUsername()));
            
            //if we don't get the user in DB we need to create it
            if ($fosUser == null)
            {
                $fosUser = $this->transZilla($user, $token);
                return $fosUser;
            }
            
            //we reset this fields to get fresh data from DC
            $fosUser->setRoles($this->transformRoles($user->getRoles()));
            //$fosUser->addRole($this->em->getRepository('ReporteUsersBundle:Role')->find(1));
            $fosUser->setArea($this->transformArea($user->getRoles()));
            //we set the password with the credentials provided and with the DB user salt, so we can get the right hash for the encoder
            $fosUser->setPassword($this->encoderFactory->getEncoder($fosUser)->encodePassword($token->getCredentials(), $fosUser->getSalt()));
            return $fosUser;
        }
        
        throw new AuthenticationException('Invalid username or password');
    }
    
    
    
    private function transZilla($user, $token)
    {
        $fosUser = new User();
        $fosUser->setUsername($user->getUsername());
        $fosUser->setFirstName($user->getFisrtName());
        $fosUser->setFullName($user->getFullName());
        $fosUser->setEmail($user->getEmail());
        $fosUser->setPassword($this->encoderFactory->getEncoder($fosUser)->encodePassword($token->getCredentials(), $fosUser->getSalt()));
        $fosUser->setRoles($this->transformRoles($user->getRoles()));
        $fosUser->setArea($this->transformArea($user->getRoles()));

        $this->em->persist($fosUser);
        $this->em->flush();
        
        return $fosUser;
    }


    /**
     * Helper function to get an Area object based on the DC group
     */
    private function transformArea($roles)
    {
        foreach ($roles as $value) 
        {
            $area = $this->em->getRepository('ReporteAreaBundle:Area')->findOneBy(array('nombre_grupo_dc' => $value));
            if ($area)
            {
                return $area;
            }
        }
    }
    
    /**
     * Helper function to get our roles based on DC groups
     */
    private function transformRoles($roles)
    {
        /*if (in_array("REPORTE_ADMIN", $roles))
        {
            return array("ROLE_ADMIN");
        }
        elseif (in_array("REPORTE_TEC", $roles))
        {
            return array("ROLE_TEC");
        }
        elseif (in_array("DOMAIN_USERS", $roles))
        {
            return array("ROLE_LIMITED");
        }
        return array();*/
        
        $myRoles = array();
        
        foreach ($roles as $value) 
        {
            $role = $this->em->getRepository('ReporteUsersBundle:Role')->findOneBy(array('nombre_grupo_dc' => $value));
            if ($role)
            {
                $myRoles[] = $role;
            }
        }
        
        return $myRoles;
    }
}