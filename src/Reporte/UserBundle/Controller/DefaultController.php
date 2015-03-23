<?php

namespace Reporte\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Reporte\UserBundle\Entity\User;
use Reporte\UserBundle\Form\UserType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    /**
     * Lista todos los usuarios, excepto root
     * 
     * @Route("/mostrar/usuario/{page}", defaults={"page" = 1})
     * @Template()
     */
    public function indexAction(Request $request,$page)
    {
        $em = $this->getDoctrine()->getManager();
        
        //paginator settings
        $paginator = $this->get('reporte_paginator');
        $paginator->setUrl('reporte_user_default_index');
        $limit = $this->container->getParameter ('items_per_page');
        $offset = ($page -1)*$this->container->getParameter ('items_per_page');
        
        $paginator->paginate($page, $em->getRepository('ReporteUserBundle:User')->getCounter());
        $users = $em->getRepository('ReporteUserBundle:User')->findAllNotRoot($limit, $offset);
        
        $form = $this->createSearchForm();
        $form->handleRequest($request);
        
        if ($form->isValid()) 
        {
            $data = $form->getData();    
            $users = $em->getRepository('ReporteUserBundle:User')->findLike($data);
            
            if (!$users)
            {
                $this->get('session')->getFlashBag()->add('warning','No existen usuarios que coincidan con su busqueda');    
                return $this->redirect($this->generateUrl('reporte_user_default_index'));
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
        
        /*$roles = $em->getRepository('ReporteUserBundle:Role')->findAll();
        $form->add('roles', 'entity', array('class' => 'ReporteUserBundle:Role','choices' => $roles,
                                           'property'=>'name','required'=>false, 'empty_value'=>'Seleccione el rol'));*/
        
        $form->add('Buscar', 'submit', array('attr' => array('class' => 'btn btn-primary')));
        
        return $form;
    }
    
    
    /**
     * Lista todo los usuarios marcados como tecnicos 
     * 
     * @Route("/mostrar/tecnicos")
     * @Template()
     */
    public function tecnicosAction()
    {
        $users = $this->getDoctrine()->getRepository('ReporteUserBundle:User')->findTecnicos();
        
        if (!$users)
        {
            $this->get('session')->getFlashBag()->add('warning','Ningun usuario existente ha sido declarado como tecnico');
            return $this->redirect($this->generateUrl("reporte_custom_default_index"));
        }
        
        return array('entities'=>$users);
    }
    
    
    /**
     * Muestra el formulario para adicionar un Usuario
     * 
     * @Route("/admin/usuario/adicionar")
     * @Template()
     */
    public function addAction(Request $request)
    {   
        $user = new User();
        $form = $this->createForm(new UserType(),$user, array('attr' => array('class' => 'form-horizontal')));
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
            return $this->redirect($this->generateUrl('reporte_user_default_index'));
        }

        return  array('form'=>$form->createView());
    }
    
    
    /**
     * Muestra el formulario para modificar un Usuario
     * 
     * @Route("/admin/usuario/modificar/{id}", defaults={"id" = 0})
     * @Template()
     */
    public function updateAction($id, Request $request)
    {         
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ReporteUserBundle:User')->find($id);
        
        if (!$user) 
        {
            $this->get('session')->getFlashBag()->add('error','No existe un Usuario con ese id');
            return $this->redirect($this->generateUrl('reporte_user_default_index'));
        }
        
        $form = $this->createForm(new UserType(), $user, array('attr' => array('class' => 'form-horizontal')));
        $form->remove('password');
        $form->handleRequest($request);
        
        if($form->isValid())
        {
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success',"Usuario modificado");
            return $this->redirect($this->generateUrl('reporte_user_default_index'));
        }
        
        return array('form'=>$form->createView());
    }
    
    
    /**
     * Muestra el formulario que permite modificar el password de un usuario.
     * Si el parametro $id = 0 (default) entonces se modifica el passwd del usuario actual
     * 
     * @Route("/usuario/modificar/clave/{id}", defaults={"id" = 0})
     * @Template()
     */
    public function passwdAction($id, Request $request)
    {         
        $em = $this->getDoctrine()->getManager();
        
        if ($id == 0)
            $id = $this->getUser()->getId();
        //si busco otro usuario entonces debo ser admin
        elseif (false === $this->get('security.context')->isGranted('ROLE_ADMIN'))
            throw new AccessDeniedException();

        $user = $em->getRepository('ReporteUserBundle:User')->find($id);
        
        if (!$user)
        {
            $this->get('session')->getFlashBag()->add('error','No existe un Usuario con ese id');
            return $this->redirect($this->generateUrl('reporte_user_default_index'));
        }
        
        $form = $this->createForm(new UserType(), $user, array('attr' => array('class' => 'form-horizontal')));
        $form->remove('username');
        $form->remove('email');
        $form->remove('area');
        $form->remove('roles');
        
        $form->handleRequest($request);
        
        if($form->isValid())
        {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success',"Clave Cambiada");
            return $this->redirect($this->generateUrl('reporte_custom_default_index'));
        }
        
        return $this->render('ReporteUserBundle:Default:passwd.html.twig',array('form'=>$form->createView()));
    }
    
    /**
    * Muestra un mensaje pidiendo la confirmacion necesaria para eliminar un Usuario
    * 
    * @Route("/admin/usuario/confirmar/eliminar/{id}", defaults={"id" = 0})
    * @Template()
    */
    public function askdeleteAction($id)
    {              
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ReporteUserBundle:User')->find($id);
        
        if (!$user)
        {
            $this->get('session')->getFlashBag()->add('error','No existe un Usuario con ese id');
            return $this->redirect($this->generateUrl('reporte_user_default_index'));
        }
        
        $this->get('session')->getFlashBag()->add('warning','Esta accion no se puede deshacer');
        return array('id'=>$id, 'nombre'=>$user->getUsername());
    }
    
    /**
     * Elimina un usuario
     * 
     * @Route("/admin/usuario/eliminar/{id}")
     * @Template()
     */
    public function deleteAction($id)
    {         
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ReporteUserBundle:User')->find($id);
        
        if (!$user)
        {
            $this->get('session')->getFlashBag()->add('error','No existe un Usuario con ese id');
            return $this->redirect($this->generateUrl('reporte_user_default_index'));
        }
        
        $em->remove($user);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add('success','Usuario eliminado');
        return $this->redirect($this->generateUrl('reporte_user_default_index'));
    }
    
    /**
     * Funcion auxiliar para mostrar el form de autenticacion en el sidebar
     */
    public function loginAction(Request $request)
    {
        //si el usuario solicita la pagina de autenticacion no mostrar el form en sidebar
        $aux = Request::createFromGlobals();
        if($aux->getPathInfo() == '/user/login')
            return new Response("");
        
        //IS_AUTHENTICATED_FULLY es dado a un usuario que ha introducido sus credenciales durante esta sesion
        //si el usuario no tiene este rol significa que no ha introducido sus credenciales
        //
         if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))     
         {
            $session = $request->getSession();
            // get the login error if there is one
            if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
                $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
            } 
            else 
            {
                $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
                $session->remove(SecurityContext::AUTHENTICATION_ERROR);
            }

            return $this->render('ReporteUserBundle:Default:login.html.twig',
                                array(
                                    // last username entered by the user
                                    'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                                    'error'=> $error,
                                )
            );
         }
         else
             return new Response("");
             
    }
    
    
    /**
     * Muestra el formulario de autenticacion
     * 
     * @Route("/user/login")
     * @Template()
    */
    public function loginPageAction(Request $request)
    {
        //chequear si el usuario no esta autenticado
        if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))     
        {
            $session = $request->getSession();
            // get the login error if there is one
            if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
                $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
            } 
            else 
            {
                $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
                $session->remove(SecurityContext::AUTHENTICATION_ERROR);
            }
                    
            return $this->render('ReporteUserBundle:Default:alllogin.html.twig',
                                array(
                                    // last username entered by the user
                                    'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                                    'error'=> $error,
                                )
            );
        }
        else
             return new Response("");
    }

}
