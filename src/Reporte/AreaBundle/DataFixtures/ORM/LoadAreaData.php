<?php

namespace Reporte\AreaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Reporte\AreaBundle\Entity\Area;

class LoadAreaData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $capacitacion = new Area();
        $capacitacion->setNombre("Division de Capacitacion e Infocomunicaciones");
        $capacitacion->setNombreGrupoDC("CAPACITACION_DOCUMENTACION");
        
        $redes = new Area();
        $redes->setNombre("Division de Redes");
        $redes->setNombreGrupoDC("DIRECCION_DE_REDES");
        
        $pe = new Area();
        $pe->setNombre("Division de Planta Exterior");
        $pe->setNombreGrupoDC("PLANTA_EXTERNA");
        
        $tecnica = new Area();
        $tecnica->setNombre("Division Tecnica de Equipos");
        $tecnica->setNombreGrupoDC("DIRECCION_TECNICA");
        
        $comercial = new Area();
        $comercial->setNombre("Vicepresidencia Comercial");
        $comercial->setNombreGrupoDC("COMERCIAL");
        
        $juridico = new Area();
        $juridico->setNombre("Vicepresidencia Juridica");
        $juridico->setNombreGrupoDC("JURIDICA");
        
        $admin = new Area();
        $admin->setNombre("Vicepresidencia Administrativa");
        $admin->setNombreGrupoDC("ADMINISTRACION");
        
        $economia = new Area();
        $economia->setNombre("Vicepresidencia Economica");
        $economia->setNombreGrupoDC("ECONOMIA");
        
        $presidencia = new Area();
        $presidencia->setNombre("Presidencia");
        $presidencia->setNombreGrupoDC("PRESIDENCIA");
        
        $rh = new Area();
        $rh->setNombre("Recursos Humanos");
        $rh->setNombreGrupoDC("R.HUMANOS");
        
        $manager->persist($capacitacion);
        $manager->persist($redes);
        $manager->persist($pe);
        $manager->persist($tecnica);
        $manager->persist($comercial);
        $manager->persist($juridico);
        $manager->persist($admin);
        $manager->persist($economia);
        $manager->persist($presidencia);
        $manager->persist($rh);
        $manager->flush();
        
        $this->addReference("area-capacitacion", $capacitacion);
        $this->addReference("area-planta_externa", $pe);
        $this->addReference("area-tecnica", $tecnica);
    }
    
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}
?>
