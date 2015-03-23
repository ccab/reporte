<?php

namespace Reporte\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Reporte\UsersBundle\Entity\User;
use Reporte\UsersBundle\Form\UserType;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class DefaultController extends Controller
{

    /**
     * Lista todos los usuarios, excepto root
     * 
     * @Route("/mostrar/usuarios/{page}", defaults={"page" = 1})
     * @Template()
     */
    public function indexAction(Request $request,$page)
    {
        $em = $this->getDoctrine()->getManager();
        
        //paginator settings
        $paginator = $this->get('reporte_paginator');
        $paginator->setUrl('reporte_users_default_index');
        $limit = $this->container->getParameter ('items_per_page');
        $offset = ($page -1)*$this->container->getParameter ('items_per_page');
        
        $paginator->paginate($page, $em->getRepository('ReporteUsersBundle:User')->getCounter());
        $users = $em->getRepository('ReporteUsersBundle:User')->findAllNotRoot($limit, $offset);
        
        $form = $this->createSearchForm();
        $form->handleRequest($request);
        
        if ($form->isValid()) 
        {
            $data = $form->getData();    
            $users = $em->getRepository('ReporteUsersBundle:User')->findLike($data);
            
            if (!$users)
            {
                $this->get('session')->getFlashBag()->add('warning','No existen usuarios que coincidan con su busqueda');    
                return $this->redirect($this->generateUrl('reporte_users_default_index'));
            }
            
            $this->get('session')->getFlashBag()->add('info','Resultados de la busqueda');
            return array('entities' => $users, 'form' => $form->createView());
        }
        
        if (!$users)
        {
            $this->get('session')->getFlashBag()->add('error','No existen usuarios');
            return array('form'=>$form->createView());
        }
        
        return array('entities' => $users, 'form' => $form->createView(), 'paginator' => $paginator);
    }
    
    
    /**
     * Helper function to create the Bundle Index search form
     */
    private function createSearchForm()
    {
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createFormBuilder(array(),array('attr' => array('class' => 'form-inline')))
                     ->add('username', 'search',array('label'=>'Nombre','required'=>false, 'attr' => array('placeholder'=>'Usuario')))
                     //->add('role','entity',array('label'=>'Rol','class'=>'ReporteUserBundle:Role','property'=>'name'))
                     ->getForm();
        
        $areas = $em->getRepository('ReporteAreaBundle:Area')->findAll();
        $form->add('area', 'entity', array('class' => 'ReporteAreaBundle:Area','choices' => $areas,
                                           'property'=>'nombre','required'=>false, 'empty_value'=>'Seleccione el area'));
        
        $form->add('Buscar', 'submit', array('attr' => array('class' => 'btn btn-primary')));
        
        return $form;
    }
    
    
    /**
     * Muestra el formulario para adicionar un Usuario
     * 
     * @Route("/admin/usuarios/adicionar")
     * @Template()
     */
    public function addAction(Request $request)
    {   
        $user = new User();
        $form = $this->createForm('reporte_usersbundle_user',$user, array('attr' => array('class' => 'form-horizontal')));
        $form->handleRequest($request);

        if($form->isValid())
        {
            //encriptando el password
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success','Usuario adicionado');
            return $this->redirect($this->generateUrl('reporte_users_default_index'));
        }

        return  array('form'=>$form->createView());
    }
    
    
    
    /**
     * Muestra el formulario para modificar un Usuario
     * 
     * @Route("/admin/usuarios/modificar/{id}", defaults={"id" = 0})
     * @Template()
     */
    public function updateAction($id, Request $request)
    {         
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ReporteUsersBundle:User')->find($id);
        
        if (!$user) 
        {
            $this->get('session')->getFlashBag()->add('alert','No existe un Usuario con ese id');
            return $this->redirect($this->generateUrl('reporte_users_default_index'));
        }
        
        $form = $this->createForm(new UserType(), $user, array('attr' => array('class' => 'form-horizontal')));
        $form->remove('password');
        $form->handleRequest($request);
        
        if($form->isValid())
        {
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success',"Usuario modificado");
            return $this->redirect($this->generateUrl('reporte_users_default_index'));
        }
        
        return array('form'=>$form->createView());
    }
    
    
    
    /**
    * Muestra un mensaje pidiendo la confirmacion necesaria para eliminar un Usuario
    * 
    * @Route("/admin/usuarios/eliminar/{id}", defaults={"id" = 0})
    * @Template()
    */
    public function askdeleteAction($id)
    {              
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ReporteUsersBundle:User')->find($id);
        
        if (!$user)
        {
            $this->get('session')->getFlashBag()->add('alert','No existe un Usuario con ese id');
            return $this->redirect($this->generateUrl('reporte_users_default_index'));
        }
        
        $this->get('session')->getFlashBag()->add('warning','Esta accion no se puede deshacer');
        return array('id'=>$id, 'nombre'=>$user->getUsername());
    }
    
    
    
    /**
     * Elimina un usuario
     * 
     * @Route("/admin/usuarios/eliminar/confirmado/{id}")
     * @Template()
     */
    public function deleteAction($id)
    {         
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ReporteUsersBundle:User')->find($id);
        
        if (!$user)
        {
            $this->get('session')->getFlashBag()->add('alert','No existe un Usuario con ese id');
            return $this->redirect($this->generateUrl('reporte_users_default_index'));
        }
        
        $em->remove($user);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add('success','Usuario eliminado');
        return $this->redirect($this->generateUrl('reporte_users_default_index'));
    }
    
    
    /**
     * Lista todo los usuarios marcados como tecnicos 
     * 
     * @Route("/mostrar/tecnico")
     * @Template()
     */
    public function tecnicoAction()
    {
        $users = $this->getDoctrine()->getRepository('ReporteUsersBundle:User')->findTecnicos();
        
        if (!$users)
        {
            $this->get('session')->getFlashBag()->add('warning','Ningun usuario existente ha sido declarado como tecnico');
            return $this->redirect($this->generateUrl("reporte_custom_default_index"));
        }
        
        return array('entities'=>$users);
    }
    
    
    
    /**
     * Muestra el formulario que permite modificar el password de un usuario.
     * Si el parametro $id = 0 (default) entonces se modifica el passwd del usuario actual
     * 
     * @Route("/usuarios/modificar/clave/{id}", defaults={"id" = 0})
     * @Template()
     */
    public function passwdAction($id, Request $request)
    {         
        $em = $this->getDoctrine()->getManager();
        
        if ($id == 0) 
        {
            $id = $this->getUser()->getId();
        }
        //si busco otro usuario entonces debo ser admin
        elseif (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) 
        {
            throw new AccessDeniedException();
        }

        $user = $em->getRepository('ReporteUsersBundle:User')->find($id);
        
        if (!$user)
        {
            $this->get('session')->getFlashBag()->add('alert','No existe un Usuario con ese id');
            return $this->redirect($this->generateUrl('reporte_users_default_index'));
        }
        
        $form = $this->createForm('reporte_usersbundle_user', $user, array('attr' => array('class' => 'form-horizontal')));        
        $form->handleRequest($request);
        
        if ($form->isValid())
        {
            $passwordMatch = false;
            $message = "Due to an error the password can't be changed. Contact your system admin";
            
            if (false === $this->get('security.context')->isGranted('ROLE_ADMIN'))
            {
                $errorList = $this->get('validator')->validateValue($form->get('oldPassword')->getData(), new UserPassword());
                if (count($errorList) == 0)
                {
                    $passwordMatch = true;
                }
                else
                {
                    $message = $errorList[0]->getMessage();
                }
            }
            
            if ($this->get('security.context')->isGranted('ROLE_ADMIN') || $passwordMatch)
            {
                //set the new user's password
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user); 
                $user->setPassword($encoder->encodePassword($form->get('newPassword')->getData(), $user->getSalt()));
                $em->flush();

                $this->get('session')->getFlashBag()->add('success',"Clave Cambiada");
                return $this->redirect($this->generateUrl('reporte_custom_default_index'));
            }
            else
            {   
                $this->get('session')->getFlashBag()->add('alert', $message);
                return $this->redirect($this->generateUrl('reporte_users_default_passwd'));
            }
        }
        
        return $this->render('ReporteUserBundle:Default:passwd.html.twig',array('form'=>$form->createView()));
    }
    
}
