<?php
namespace Reporte\UserBundle\Listener;

use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Custom login listener.
 */
class LoginListener
{
	private $securityContext;
	private $em;
	
	/**
	 * Constructor
	 * 
	 * @param SecurityContext $securityContext
	 * @param Doctrine        $doctrine
	 */
	public function __construct(SecurityContext $securityContext, EntityManager $em)
	{
		$this->securityContext = $securityContext;
		$this->em = $em;
	}
	
	/**
	 * Funcion para setear los valores de las propiedades login_time & last_access de la entidad User
	 * 
	 * @param InteractiveLoginEvent $event
	 */
	public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
	{
		if ($this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')) 
                {
                    $user = $event->getAuthenticationToken()->getUser(); 
                    $user->setLastAccess($user->getLastLogin());
                    //$user->setLastAccess($user->getLoginTime());
                    //$user->setLoginTime(new \DateTime("now"));
                    $this->em->flush();
		}
	}
}
?>
