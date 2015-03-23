<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Reporte\UsersBundle\Entity\Role;

/**
 * Migracion para crear los roles en la nueva tabla roles_reporte, tambien se 
 * modifica la tabla areas adicionando los correspondientes grupos en DC
 */
class Version20141029162657 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    public function up(Schema $schema)
    {
        $em = $this->container->get('doctrine.orm.entity_manager'); 			
        
        //Roles
        $admin = new Role();
        $admin->setName('Administrador');
        $admin->setRole('ROLE_ADMIN');
        $admin->setNombreGrupoDC('REPORTE_ADMIN');
        
        $tec = new Role();
        $tec->setName('Tecnico');
        $tec->setRole('ROLE_TEC');
        $tec->setNombreGrupoDC('REPORTE_TEC');
        
        $lim = new Role();
        $lim->setName('Limitado');
        $lim->setRole('ROLE_LIMITED');
        $lim->setNombreGrupoDC('REPORTE_LIMITADO');
        
        $user = new Role();
        $user->setName('Usuario');
        $user->setRole('ROLE_USER');
        $user->setNombreGrupoDC('DOMAIN_USERS');
        
        $em->persist($admin);
        $em->persist($tec);
        $em->persist($lim);
        
        //Areas
        $capacitacion = $em->getRepository('ReporteAreaBundle:Area')->findOneBy(array('nombre' => "Division de Capacitacion e Infocomunicaciones"));
        $capacitacion->setNombreGrupoDC("CAPACITACION_DOCUMENTACION");
        
        $pe = $em->getRepository('ReporteAreaBundle:Area')->findOneBy(array('nombre' => "Division de Planta Exterior"));
        $pe->setNombreGrupoDC("PLANTA_EXTERNA");
        
        $redes = $em->getRepository('ReporteAreaBundle:Area')->findOneBy(array('nombre' => "Division de Redes"));
        $redes->setNombreGrupoDC("DIRECCION_DE_REDES");
        
        $tecnica = $em->getRepository('ReporteAreaBundle:Area')->findOneBy(array('nombre' => "Division Tecnica de Equipos"));
        $tecnica->setNombreGrupoDC("DIRECCION_TECNICA");
        
        $admon = $em->getRepository('ReporteAreaBundle:Area')->findOneBy(array('nombre' => "Vicepresidencia Administrativa"));
        $admon->setNombreGrupoDC("ADMINISTRACION");
        
        $comercial = $em->getRepository('ReporteAreaBundle:Area')->findOneBy(array('nombre' => "Vicepresidencia Comercial"));
        $comercial->setNombreGrupoDC("COMERCIAL");
        
        $rh = $em->getRepository('ReporteAreaBundle:Area')->findOneBy(array('nombre' => "Recursos Humanos"));
        $rh->setNombreGrupoDC("R.HUMANOS");
        
        $presidencia = $em->getRepository('ReporteAreaBundle:Area')->findOneBy(array('nombre' => "Presidencia"));
        $presidencia->setNombreGrupoDC("PRESIDENCIA");
        
        $economia = $em->getRepository('ReporteAreaBundle:Area')->findOneBy(array('nombre' => "Vicepresidencia Economica"));
        $economia->setNombreGrupoDC("ECONOMIA");
        
        $juridica = $em->getRepository('ReporteAreaBundle:Area')->findOneBy(array('nombre' => "Vicepresidencia Juridica"));
        $juridica->setNombreGrupoDC("JURIDICA");

        $em->flush();
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
