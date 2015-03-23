<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Reporte\UserBundle\ReporteUserBundle(),
            new Reporte\CustomBundle\ReporteCustomBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Reporte\MarcaBundle\ReporteMarcaBundle(),
            new Reporte\AreaBundle\ReporteAreaBundle(),
            new Reporte\PCBundle\ReportePCBundle(),
            new Reporte\ServicioBundle\ReporteServicioBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Reporte\MenuBundle\ReporteMenuBundle(),
            new Reporte\ReporteServicioBundle\ReporteReporteServicioBundle(),
            new Reporte\PaginatorBundle\ReportePaginatorBundle(),
            new Reporte\UsersBundle\ReporteUsersBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle()
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
