<?php

namespace Reporte\MarcaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Reporte\MarcaBundle\Entity\Marca;

class LoadMarcaData implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $marca = new Marca();
        $marca->setNombre("N/A");
        
        $manager->persist($marca);
        $manager->flush();
    }
}
?>
