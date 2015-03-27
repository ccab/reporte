<?php
namespace Reporte\MenuBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\Menu\Util\MenuManipulator;
use \RecursiveIteratorIterator;
use Knp\Menu\Iterator\RecursiveItemIterator;
use Knp\Menu\Iterator\CurrentItemFilterIterator;
use \ArrayIterator;
use Knp\Menu\MenuItem;

class MenuBuilder extends ContainerAware
{
    public function navigation(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('home', array('route' => 'reporte_custom_default_index', 'label' => "Inicio"));
        $menu['home']->setChildrenAttributes(array('class' => 'nav nav-pills nav-stacked'));
        
        //Orden de Trabajo
        $menu['home']->addChild('ot', array('route' => 'reporte_servicio_servicio_index', 'label' => "Orden de Trabajo"));
        $menu['home']['ot']->addChild('sh', array('route' => 'reporte_servicio_servicio_show', 'label' => "Ver OT"));
        $menu['home']['ot']->addChild('add', array('route' => 'reporte_servicio_servicio_add', 'label' => "Adicionar OT"));
        $menu['home']['ot']->addChild('upd', array('route' => 'reporte_servicio_servicio_update','label' => "Modificar OT"));
        $menu['home']['ot']->addChild('sol', array('route' => 'reporte_servicio_servicio_solve','label' => "Solucionar OT"));
        $menu['home']['ot']->addChild('del', array('route' => 'reporte_servicio_servicio_askdelete','label' => "Eliminar OT"));
        $menu['home']['ot']->setDisplayChildren(false);
                                                                                      
        //Usuario
        /*$menu['home']->addChild('Tecnico', array('route' => 'reporte_user_default_tecnicos'));
        $menu['home']->addChild('passwd', array('route' => 'reporte_user_default_passwd', 'label' => 'Cambiar Clave'));
        $menu['home']['passwd']->setDisplay(false);*/
        $menu['home']->addChild('login', array('route' => 'reporte_user_default_loginpage', 'label' => 'Iniciar Sesion'));
        $menu['home']['login']->setDisplay(false);
        
        //Usuario (nuevo)
        $menu['home']->addChild('Tecnico', array('route' => 'reporte_users_default_tecnico'));
        $menu['home']->addChild('passwd2', array('route' => 'reporte_users_default_passwd', 'label' => 'Cambiar Clave'));
        $menu['home']['passwd2']->setDisplay(false);
        
        //Reporte
        $menu['home']->addChild('Reporte', array('route' => 'reporte_reporteservicio_reporteservicio_index'));
        $menu['home']['Reporte']->addChild('sh', array('route' => 'reporte_reporteservicio_reporteservicio_show','label'=>'Ver Reporte'));
        $menu['home']['Reporte']->addChild('upd', array('route' => 'reporte_reporteservicio_reporteservicio_update','label'=>'Modificar Reporte'));
        $menu['home']['Reporte']->setDisplayChildren(false);
        
        if($this->container->get('security.context')->isGranted('ROLE_ADMIN'))
        {
                       
            //Computadora
            $menu['home']->addChild('pc', array('route' => 'reporte_pc_pc_index','label' => 'Computadora'));
            $menu['home']['pc']->addChild('add', array('route' => 'reporte_pc_pc_add', 'label' => "Adicionar PC"));
            $menu['home']['pc']->addChild('upd', array('route' => 'reporte_pc_pc_update', 'label' => "Modificar PC"));
            $menu['home']['pc']->addChild('del', array('route' => 'reporte_pc_pc_askdelete', 'label' => "Eliminar PC"));
            $menu['home']['pc']->setDisplayChildren(false);
            
            //Usuario
            /*$menu['home']->addChild('user', array('route' => 'reporte_user_default_index','label' => 'Usuario'));
            $menu['home']['user']->addChild('add', array('route' => 'reporte_user_default_add', 'label' => "Adicionar Usuario"));
            $menu['home']['user']->addChild('upd', array('route' => 'reporte_user_default_update', 'label' => "Modificar Usuario"));
            $menu['home']['user']->addChild('del', array('route' => 'reporte_user_default_askdelete', 'label' => "Eliminar Usuario"));
            $menu['home']['user']->setDisplayChildren(false);*/
            
            //Usuario (nuevo)
            $menu['home']->addChild('user2', array('route' => 'reporte_users_default_index','label' => 'Usuario'));
            $menu['home']['user2']->addChild('add', array('route' => 'reporte_users_default_add', 'label' => "Adicionar Usuario"));
            $menu['home']['user2']->addChild('upd', array('route' => 'reporte_users_default_update', 'label' => "Modificar Usuario"));
            $menu['home']['user2']->addChild('del', array('route' => 'reporte_users_default_askdelete', 'label' => "Eliminar Usuario"));
            $menu['home']['user2']->setDisplayChildren(false);
        }
        
        if($this->container->get('security.context')->isGranted('ROLE_TEC'))
        {
            $menu['home']->addChild('Reporte Mensual', array('route' => 'reporte_reporteservicio_reporteservicio_mensual',
                                                     'routeParameters' => array('month' => 0)));
            $menu['home']['Reporte Mensual']->setLinkAttribute('target', '_blank');
        }
        
        //$menu['home']->addChild('Salir', array('route' => 'logout'));
        
        return $menu;
    }
    
    public function breadcrumbs(FactoryInterface $factory, array $options)
    {
        $menu = $this->navigation($factory, $options);

        $matcher = $this->container->get('knp_menu.matcher');
        $voter = $this->container->get('knp_menu.voter.router');
        $matcher->addVoter($voter);

        $treeIterator = new RecursiveIteratorIterator(new RecursiveItemIterator(new ArrayIterator(array($menu))),
                                                      RecursiveIteratorIterator::SELF_FIRST);

        $iterator = new CurrentItemFilterIterator($treeIterator, $matcher);

        // Set Current as an empty Item in order to avoid exceptions on knp_menu_get
        $current = new MenuItem('', $factory);

        foreach ($iterator as $item) 
        {
            $item->setCurrent(true);
            $current = $item;
            break;
        }

        //send the breadcrumbs trail array inside the bc attribute
        $breadcrumbs = new MenuManipulator();
        $aux = $breadcrumbs->getBreadcrumbsArray($current);
        //deleting the root item
        unset($aux[0]);
        $current->setAttribute('bc', $aux);
        
        return $current;
    }
    
    
   
}