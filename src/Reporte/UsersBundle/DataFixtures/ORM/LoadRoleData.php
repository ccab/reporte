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
               ->setNombreGrupoDC('REPORTE_ADMIN');
        
        $tecnico= new Role();
        $tecnico->setName('Tecnico')
               ->setRole('ROLE_TEC')
               ->setNombreGrupoDC('REPORTE_TEC');
        
        $limitado = new Role();
        $limitado->setName('Limitado')
               ->setRole('ROLE_LIMITED')
               ->setNombreGrupoDC('REPORTE_LIMITADO');
        
        $user = new Role();
        $user->setName('Usuario')
             ->setRole('ROLE_USER')
             ->setNombreGrupoDC("DOMAIN_USERS");
        
        $manager->persist($admin);
        $manager->persist($tecnico);
        $manager->persist($limitado);
        $manager->persist($user);
        $manager->flush();
        
        $this->addReference("rol-administrador", $admin);
    }
    
    
    public function getOrder() 
    {
        return 2;
    }

}
