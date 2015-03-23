<?php

namespace Reporte\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Reporte\UserBundle\Entity\User;
use Reporte\UserBundle\Entity\Role;


class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    public function load(ObjectManager $manager)
    {
        $role_admin = new Role();
        $role_admin->setName("Administrador");
        $role_admin->setRole("ROLE_ADMIN");
        
        $role_limitado = new Role();
        $role_limitado->setName("Limitado");
        $role_limitado->setRole("ROLE_LIMITED");
        
        $tecnico = new Role();
        $tecnico->setName("Tecnico");
        $tecnico->setRole("ROLE_TEC");
        
        //usuario root
        $root = new User();
        $root->setUsername('root');
        $root->addRole($role_admin);
        
        //referencia del fixtures de area
        $root->setArea($this->getReference("area-capacitacion"));
        
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($root);
        $password = $encoder->encodePassword('root2014', $root->getSalt());
        
        $root->setPassword($password);
        $root->setEmail('crafael@cubatel.cu');
        
        //usuario sheila
        $sheila = new User();
        $sheila->setUsername('sheila');
        $sheila->addRole($role_admin);
        
        //referencia del fixtures de area
        $sheila->setArea($this->getReference("area-capacitacion"));
        
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($sheila);
        $password = $encoder->encodePassword('sheilareportes', $sheila->getSalt());
        
        $sheila->setPassword($password);
        $sheila->setEmail('sheila@cubatel.cu');

        $manager->persist($role_admin);
        $manager->persist($role_limitado);
        $manager->persist($tecnico);
        $manager->persist($root);
        $manager->persist($sheila);
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 2;  // the order in which fixtures will be loaded
    }

}
?>
