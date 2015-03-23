<?php
namespace Reporte\UsersBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Reporte\UsersBundle\Entity\Role;

class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager) 
    {
        $admin = new Role();
        $admin->setName('Administrador')
               ->setRole('ROLE_ADMIN')
               ->setNombreGrupoDC('ADMIN_COSTO');
        
        $comercial = new Role();
        $comercial->setName('Comercial')
                  ->setRole('ROLE_COMERCIAL')
                  ->setNombreGrupoDC('COMERCIAL_COSTO');
        
        $user = new Role();
        $user->setName('Usuario')
             ->setRole('ROLE_USER')
             ->setNombreGrupoDC("DOMAIN_USERS");
        
        $manager->persist($admin);
        $manager->persist($comercial);
        $manager->persist($user);
        $manager->flush();
        
        $this->addReference("rol-administrador", $admin);
    }
    
    
    public function getOrder() 
    {
        return 2;
    }

}
