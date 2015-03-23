<?php

namespace Reporte\ServicioBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reporte\ServicioBundle\Entity\Servicio;
use Reporte\ReporteServicioBundle\Entity\ReporteServicio;
use Symfony\Component\HttpFoundation\Response;

class ServicioController extends Controller
{
    /** 
     * List all Services sorted by date, if the user has ADMIN or TECNICO role he can see all Services from all Areas, 
     * otherwise he only can see the Services of his own Area
     * 
     * @Route("/mostrar/ot/{page}", defaults={"page" = 1})
     * @Template()
     */
    public function indexAction(Request $request, $page)
    {
        $em = $this->getDoctrine()->getManager();
        
        //paginator settings
        $paginator = $this->get('reporte_paginator');
        $paginator->setUrl('reporte_servicio_servicio_index');
        $limit = $this->container->getParameter ('items_per_page');
        $offset = ($page -1)*$this->container->getParameter ('items_per_page');
        
        $form = $this->createSearchForm();
        $form->handleRequest($request);
        
        if ($form->isValid()) 
        {
            $data = $form->getData();    
            
            if (true === $this->get('security.context')->isGranted('ROLE_ADMIN') || true === $this->get('security.context')->isGranted('ROLE_TEC')) 
            {
                $entities = $em->getRepository('ReporteServicioBundle:Servicio')->findOTLike($data);
            } 
            else 
            {
                $entities = $em->getRepository('ReporteServicioBundle:Servicio')->findOTArea(array('area' => $this->getUser()->getArea(),
                    'user' => $data['usuario']));
            }
            if (!$entities)
            {
                $this->get('session')->getFlashBag()->add('warning','No existen OT que coincidan con su busqueda');    
                return $this->redirect($this->generateUrl('reporte_servicio_servicio_index'));
            }
                       
            foreach ($entities as $entity) 
            {
                if ($entity->getFechaReporte() > $this->getUser()->getLastAccess()) 
                {
                    $entity->setNew(true);
                } 
                else 
                {
                    $entity->setNew(false);
                }
            }
            
            $this->get('session')->getFlashBag()->add('info','Resultados de la busqueda');
            return array('entities' => $entities, 'form' => $form->createView());
        }
        
        
        if (true === $this->get('security.context')->isGranted('ROLE_ADMIN') || true === $this->get('security.context')->isGranted('ROLE_TEC'))
        {
            $paginator->paginate($page, $em->getRepository('ReporteServicioBundle:Servicio')->getCounter());
            $entities = $em->getRepository('ReporteServicioBundle:Servicio')->findBy($criteria = array(),
                                                                                     $orderBy = array('fecha_reporte'=>'DESC'),
                                                                                     $limit, $offset);
        }
            
        else 
        {
            $paginator->paginate($page, $em->getRepository('ReporteServicioBundle:Servicio')->getCounterOTArea(array('area' => $this->getUser()->getArea())));
            $entities = $em->getRepository('ReporteServicioBundle:Servicio')->findOTArea($criteria = array('area' => $this->getUser()->getArea()),
                                                                                         $limit, $offset);
        }
            

        if (!$entities)
        {
            $this->get('session')->getFlashBag()->add('warning','No existen Ordenes de Trabajo');    
            return array();
        }

        //marking the Services as new based on the user last access
        foreach ($entities as $entity) 
        {
            if ($entity->getFechaReporte() > $this->getUser()->getLastAccess())
                $entity->setNew(true);
            else
                $entity->setNew(false);
        }
        
        return array('entities' => $entities, 'form' => $form->createView(), 'paginator' => $paginator);
    }
    
    
    /**
     * Helper function to create the Bundle Index search form
     */
    private function createSearchForm()
    {
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createFormBuilder(array(),array('attr' => array('class' => 'form-inline')))
                     ->add('usuario', 'search',array('required'=>false, 'attr' => array('placeholder'=>'Usuario', 'role'=>'form')))
                     ->getForm();
        
        //the upper roles may filter by Area
        if (true === $this->get('security.context')->isGranted('ROLE_ADMIN') || true === $this->get('security.context')->isGranted('ROLE_TEC'))
        {
            $areas = $em->getRepository('ReporteAreaBundle:Area')->findAll();
            $form->add('area', 'entity', array('class' => 'ReporteAreaBundle:Area','choices' => $areas,
                                             'property'=>'nombre','required'=>false, 'empty_value'=>'Seleccione el area'));            
        }
            
        $form->add('Buscar', 'submit', array('attr' => array('class' => 'btn btn-primary')));
        
        return $form;
    }

    /**
     * Muestra el formulario para adicionar un Servicio
     * 
     * @Route("/admin/ot/adicionar")
     * @Template()
     */
    public function addAction(Request $request)
    {           
        $entity = new Servicio();
        $form = $this->createForm('servicio_form',$entity, array('attr' => array('class' => 'form-horizontal')));  
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $entity->setAutor($this->getUser());
            $entity->setFechaReporte(new \DateTime('now'));
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success','Orden de Trabajo adicionada');
            return $this->redirect($this->generateUrl('reporte_servicio_servicio_index'));
        }

        return array('form' => $form->createView());
    }
    
    
    
    /**
     * Muestra el formulario para modificar un Servicio
     *
     * @Route("admin/ot/modificar/{id}", defaults={"id" = 0})
     * @Template()
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ReporteServicioBundle:Servicio')->find($id);

        if (!$entity)
        {
            $this->get('session')->getFlashBag()->add('error','No existe un OT con ese id');
            return $this->redirect($this->generateUrl('reporte_servicio_servicio_index'));
        }
        
        $session = $request->getSession();
        $session->set('op', 'update');
        $form = $this->createForm('servicio_form',$entity, array('attr' => array('class' => 'form-horizontal')));        
        $form->handleRequest($request);

        if ($form->isValid()) 
        {
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success','Servicio modificado');
            return $this->redirect($this->generateUrl('reporte_servicio_servicio_index'));
        }

        return array('form' => $form->createView());
    }
    
    

    /**
     * Busca y muestra un Servicio dado su id
     *
     * @Route("/admin/mostrar/ot/{id}", defaults={"id" = 0})
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ReporteServicioBundle:Servicio')->find($id);

        if (!$entity)
        {
            $this->get('session')->getFlashBag()->add('error','No existe un OT con ese id');
            return $this->redirect($this->generateUrl('reporte_servicio_servicio_index'));
        }

        return array('entity' => $entity);
    }
    
    
    /**
     * Busca y muestra un Servicio dado su id en formato de impresion
     * 
     * @Route("admin/ot/imprimir/{id}")
     * @Template()
     */
    public function printAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ReporteServicioBundle:Servicio')->find($id);

        if (!$entity)
        {
            $this->get('session')->getFlashBag()->add('error','No existe un OT con ese id');
            return $this->redirect($this->generateUrl('reporte_servicio_servicio_index'));
        }

        return array('entity' => $entity);
    }

    
    
    /**
     * Muestra el formulario para solucionar un Servicio
     * 
     * @Route("/admin/ot/solucionar/{id}", defaults={"id" = 0})
     * @Template()
     */
    public function solveAction($id, Request $request)
    {         
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ReporteServicioBundle:Servicio')->find($id);
        
        if (!$entity)
        {
            $this->get('session')->getFlashBag()->add('error','No existe un OT con ese id');
            return $this->redirect($this->generateUrl('reporte_servicio_servicio_index'));
        }
        
        $session = $request->getSession();
        $session->set('op', 'solve');
        $form = $this->createForm('servicio_form',$entity, array('attr' => array('class' => 'form-horizontal'), 'validation_groups' => array('solve')));                                   
        $form->handleRequest($request);
        
        if($form->isValid())
        {
            //Actually deprecated because of validation groups
            //server-side validation
            if($entity->getLaborRealizada() === null)
            {
                $this->get('session')->getFlashBag()->add('alert',"La labor realizada no puede enviarse vacia");
                return $this->redirect($this->generateUrl('reporte_servicio_servicio_index'));
            }
            if($entity->getPc() === null)
            {
                $this->get('session')->getFlashBag()->add('alert',"No puede solucionar la OT sin asignarle una PC");
                return $this->redirect($this->generateUrl('reporte_servicio_servicio_index'));
            }
            elseif($entity->getTecnico() === null)
            {
                $this->get('session')->getFlashBag()->add('alert',"No puede solucionar la OT sin asignarle un Tecnico");
                return $this->redirect($this->generateUrl('reporte_servicio_servicio_index'));
            }
            
            $this->createReporteServicio($em, $entity);
            
            $em->remove($entity);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success',"Orden de Trabajo solucionada");
            return $this->redirect($this->generateUrl('reporte_servicio_servicio_index'));
        }
        
        $this->get('session')->getFlashBag()->add('info',"Si la PC no existe puede crearla directamente desde aqui");
        return array('form'=>$form->createView());
    }


    /*
     * Funcion auxiliar que permite crear una nueva instancia de ReporteServicio, 
     * basada en los datos de la OT
     * (Tambien se usa para importar los datos del ReporteBundle e importarlos 
     * debido a que el objeto de Reporte es un clon de un objeto de Servicio)
     */
    public function createReporteServicio($em, $entity)
    {
        
        $rs = new ReporteServicio();
        $rs->setPcInv($entity->getPc()->getInv());
        $rs->setPcAreaNombre($entity->getPc()->getArea()->getNombre());
        $rs->setPcMarcaNombre($entity->getPc()->getMarca()->getNombre());
        $rs->setEsLaptop($entity->getPc()->getEsLaptop());
        $rs->setAutorUsername($entity->getAutor()->getUsername());
        $rs->setAutorAreaNombre($entity->getAutor()->getArea()->getNombre());
        $rs->setTecnicoUsername($entity->getTecnico()->getUsername());
        $rs->setTecnicoAreaNombre($entity->getTecnico()->getArea()->getNombre());
        $rs->setUsuario($entity->getUsuario());
        $rs->setProblema($entity->getProblema());
        $rs->setLaborRealizada($entity->getLaborRealizada());
        $rs->setPiezas($entity->getPiezas());
        $rs->setFechaReporte($entity->getFechaReporte());
        $rs->setFechaSolucion($entity->getFechaSolucion());
        $rs->setObservacion($entity->getObservacion());

        //si se escribe una pieza y no se marca la casilla de pieza
        //recuperada, se debe setear las piezas recuperadas como false
        if($entity->getPiezas() != null){
            if($entity->getPiezasRecuperadas() == null || $entity->getPiezasRecuperadas() == false)
                $rs->setPiezasRecuperadas(false);
            else
                $rs->setPiezasRecuperadas(true);
        }
        //si no se escribe una pieza entonces no se puede marcar como recuperada
        else
            $rs->setPiezasRecuperadas(null);

        $em->persist($rs);
    }
        
    

    /**
      * Muestra un mensaje pidiendo la confirmacion necesaria para eliminar un Servicio
      * 
      * @Route("/admin/ot/confirmar/eliminar/{id}", defaults={"id" = 0})
      * @Template()
     */
    public function askdeleteAction($id)
    {              
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ReporteServicioBundle:Servicio')->find($id);
        
        if (!$entity)
        {
            $this->get('session')->getFlashBag()->add('error','No existe un OT con ese id');
            return $this->redirect($this->generateUrl('reporte_servicio_servicio_index'));
        }
        
        $this->get('session')->getFlashBag()->add('warning','Esta accion no se puede deshacer');
        return array('id'=>$id);
    }
    
    /**
     * Elimina un Servicio
     * 
     * @Route("/admin/ot/eliminar/{id}")
     * @Template()
     */
    public function deleteAction($id)
    {         
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ReporteServicioBundle:Servicio')->find($id);
        
        if (!$entity)
        {
            $this->get('session')->getFlashBag()->add('error','No existe un OT con ese id');
            return $this->redirect($this->generateUrl('reporte_servicio_servicio_index'));
        }
        
        $em->remove($entity);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add('success','Orden de Trabajo eliminada');
        return $this->redirect($this->generateUrl('reporte_servicio_servicio_index'));
    }
    
}
