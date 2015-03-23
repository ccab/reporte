<?php

namespace Reporte\PCBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reporte\PCBundle\Entity\PC;
use Reporte\PCBundle\Form\PCType;

class PCController extends Controller
{

    /**
     * Lista todas las PCs ordenadas por numero de inventario
     *
     * @Route("/mostrar/pc/{page}", defaults={"page" = 1})
     * @Template()
     */
    public function indexAction(Request $request, $page)
    {
        $em = $this->getDoctrine()->getManager();
        //paginator settings
        $paginator = $this->get('reporte_paginator');
        $paginator->setUrl('reporte_pc_pc_index');
        $limit = $this->container->getParameter ('items_per_page');
        $offset = ($page -1)*$this->container->getParameter ('items_per_page');
        
        
        $form = $this->createSearchForm();
        $form->handleRequest($request);
        if ($form->isValid()) 
        {
            $data = $form->getData();    
            $entities = $em->getRepository('ReportePCBundle:PC')->findLike($data);
            
            if (!$entities)
            {
                $this->get('session')->getFlashBag()->add('warning','No existen computadoras que coincidan con su busqueda');    
                return $this->redirect($this->generateUrl('reporte_pc_pc_index'));
            }
            
            $this->get('session')->getFlashBag()->add('info','Resultados de la busqueda');
            return array('entities' => $entities, 'form' => $form->createView());
        }
        
        
        $paginator->paginate($page, $em->getRepository('ReportePCBundle:PC')->getCounter());
        $entities = $em->getRepository('ReportePCBundle:PC')->findBy($criteria = array(),
                                                                     $orderBy = array('inv'=>'ASC'),
                                                                     $limit, $offset);
        
        if (!$entities)
        {
            $this->get('session')->getFlashBag()->add('warning','No existen Computadoras');    
            return array();
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
                     ->add('inv', 'search', array('label'=>'','required'=>false, 'attr' => array('placeholder'=>'Inventario')))
                     ->getForm();
        
        $areas = $em->getRepository('ReporteAreaBundle:Area')->findAll();
        $form->add('area', 'entity', array('class' => 'ReporteAreaBundle:Area','choices' => $areas,
                                           'property'=>'nombre','required'=>false, 'empty_value'=>'Seleccione el area'));
        
        $form->add('Buscar', 'submit', array('attr' => array('class' => 'btn btn-primary')));
        
        return $form;
    }
    
    /**
     * Muestra el formulario para adicionar una PC
     *
     * @Route("/admin/pc/adicionar")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $entity = new PC();
        $form = $this->createForm(new PCType(), $entity, array('attr' => array('class' => 'form-horizontal')));
        $form->handleRequest($request);

        if ($form->isValid()) 
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success','Computadora adicionada');
            return $this->redirect($this->generateUrl('reporte_pc_pc_index'));
        }

        return array('entity' => $entity, 'form' => $form->createView());
    }

    
    /**
     * Muestra el formulario para modificar una PC
     *
     * @Route("admin/pc/modificar/{id}", defaults={"id" = 0})
     * @Template()
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ReportePCBundle:PC')->find($id);

        if (!$entity) 
        {
            $this->get('session')->getFlashBag()->add('error','No existe una PC con ese id');
            return $this->redirect($this->generateUrl('reporte_pc_pc_index'));
        }

        $form = $this->createForm(new PCType(), $entity, array('attr' => array('class' => 'form-horizontal')));
        $form->handleRequest($request);

        if ($form->isValid()) 
        {
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success',"Computadora modificada");
            return $this->redirect($this->generateUrl('reporte_pc_pc_index'));
        }

        return array('form' => $form->createView());
    }
    
    /**
     * Muestra un mensaje pidiendo la confirmacion necesaria para eliminar una PC
     * 
     * @Route("/admin/pc/confirmar/eliminar/{id}", defaults={"id" = 0})
     * @Template()
     */
    public function askdeleteAction($id)
    {              
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ReportePCBundle:PC')->find($id);
        
        if (!$entity)
        {
            $this->get('session')->getFlashBag()->add('error','No existe una PC con ese id');
            return $this->redirect($this->generateUrl('reporte_pc_pc_index'));
        }
        
        $this->get('session')->getFlashBag()->add('warning','Esta accion no se puede deshacer');
        return array('id'=>$id, 'inv'=>$entity->getInv());
    }
    
    
    /**
     * Elimina una PC
     *
     * @Route("/admin/pc/eliminar/{id}", defaults={"id" = 0})
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ReportePCBundle:PC')->find($id);
        
        if (!$entity) 
        {
            $this->get('session')->getFlashBag()->add('error','No existe una PC con ese id');
            return $this->redirect($this->generateUrl('reporte_pc_pc_index'));
        }
        
        $this->get('session')->getFlashBag()->add('success','Computadora eliminada');
        
        $em->remove($entity);
        $em->flush();
        
        return $this->redirect($this->generateUrl('reporte_pc_pc_index'));
    }

   
}
