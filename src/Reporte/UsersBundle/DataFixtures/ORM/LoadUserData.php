<?php
namespace Reporte\UsersBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Reporte\UsersBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    public function load(ObjectManager $manager)
    {
        //usuario root
        $root = new User();
        $root->setUsername('root');
        $root->setFirstName("root");
        $root->setFullName("Root");
        $root->setArea($this->getReference("area-capacitacion"));
        $root->addRole($this->getReference("rol-administrador"));
        
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($root);
        $password = $encoder->encodePassword('root2014', $root->getSalt());
        
        $root->setPassword($password);
        $root->setEmail('root@cubatel.cu');
        
        $this->addReference('root', $root);
        
        $manager->persist($root);
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 3; 
    }

}

