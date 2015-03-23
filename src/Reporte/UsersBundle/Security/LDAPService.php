<?php
namespace Reporte\UsersBundle\Security;

use adLDAP\adLDAP;
use adLDAP\adLDAPException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;

/**
 * Base class to work with adLDAP library via the service container
 *
 * @author rafael
 */
class LDAPService 
{
   
    private $username;
    private $password;
    private $base_dn;
    private $account_suffix;
    private $domain_controller;
    private $adLDAP;


    public function __construct($username, $password, $base_dn, $account_suffix, $domain_controller) 
    {
        $this->username = $username;
        $this->password = $password;
        $this->account_suffix = '@'.$account_suffix;
        $this->domain_controller = $domain_controller;
        
       
        $this->base_dn = "";
        for ($i = 0, $count = count($base_dn); $i < $count; $i++) 
        {
            if ($i == $count-1)
                $this->base_dn .= "$base_dn[$i]";
            else
                $this->base_dn .= "$base_dn[$i],";
        }
        
        try 
        {
            $this->adLDAP = new adLDAP(array('base_dn' => $this->base_dn, 
                                             'account_suffix' => $this->account_suffix,
                                             'domain_controllers' => array($this->domain_controller)));
        }
        catch (adLDAPException $e) 
        {
            throw new AuthenticationServiceException("It is not possible to connect with DC Server, check your settings".$e->getMessage());
        }
        
    }
    
    /**
     * Finds a user in the DC Server 
     * 
     * @return adLDAPUserCollection The User object with all the information available 
     */
    public function find($username)
    {
        $authUser = $this->adLDAP->user()->authenticate($this->username, $this->password);

        if ($authUser == true) 
        {
            try 
            {
                $userData = $this->adLDAP->user()->infoCollection($username,array("*"));
                
                return $userData;
            }
            catch (AuthenticationServiceException $e) 
            {
                throw new AuthenticationServiceException("It is not possible to connect with DC Server".$e->getMessage());
            }
            
        }
            
        throw new AuthenticationServiceException("It is not possible to connect with DC Server");
    }
    
    /**
     * Attempts to authenticate a user against the DC Server
     */
    public function authenticate($username, $password)
    {
        return $this->adLDAP->authenticate($username, $password);
    }
    
    /**
     * Get the groups the user is a member of
     */
    public function groups($username)
    {      
        $groups = $this->adLDAP->user()->groups($username);
        
        $sfRoles = array();
        $sfRolesTemp = array();
        foreach ($groups as $r) 
        {
            if (in_array($r, $sfRolesTemp) === false) 
            {
                //if you want symfony's roles format
                //$sfRoles[] = 'ROLE_' . strtoupper(str_replace(' ', '_', $r));
                $sfRoles[] = strtoupper(str_replace(' ', '_', $r));
                $sfRolesTemp[] = $r;
            }
        }
        
        return $sfRoles;
    }
    
}

