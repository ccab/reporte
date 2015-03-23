<?php
/*
 * Permite modificar la manera en que se crean los campos necesarios en cada momento que se solicita el formulario. 
 * A traves de events puede tener el control de como se crearan los campos basados en el usuario actual,  
 * la request y las clases del repositorio
 *
 * Based on: How to Dynamically Modify Forms Using Form Events
 * 
 * @author carlos
 */
namespace Reporte\ServicioBundle\Form;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;


class ServicioTypeSubscriber implements EventSubscriberInterface{
    
    private $securityContext;
    private $request;
    private $em;
    
    public function __construct(SecurityContext $securityContext, Request $request, EntityManager $em)
    {
        $this->securityContext = $securityContext;
        $this->request = $request;
        $this->em = $em;
        
    }

    /*
     *  Tells the dispatcher that you want to listen on the form.pre_set_data
     *  event and that the preSetData method should be called.
     */
    public static function getSubscribedEvents(){
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }  
    
    /*
     * Funcion disparada con el evento PRE_SET_DATA, en el momento de crear los campos del formulario
     * tengo 3 casos diferentes
     * 1- Cuando el usuario crea el Servicio: se decide encuestando al objeto subyacente, si es null 
     *    entonces es que no existe en BD
     * 2- Cuando se modifica el Servicio: se decide por la variable de session $op = 'update'
     * 3- Cuando se soluciona el Servicio: se decide por la variable de session $op = 'solve'
     */
    public function preSetData(FormEvent $event){
        $entity = $event->getData();
        $form = $event->getForm();
        
         //grab the user, do a quick sanity check that one exists
        $user = $this->securityContext->getToken()->getUser();
        if (!$user) 
        {
            throw new \LogicException('Usuario no autenticado!');
        }

        //si el objeto esta siendo creado por el usuario
        if (!$entity || null === $entity->getId())
        {
            //buscar las pcs del area del usuario actual
            $pcs = $this->em->getRepository('ReportePCBundle:PC')->findBy(array('area' => $user->getArea()->getId()),
                                                                          array('inv' => 'ASC'));
            $form->add('pc', 'entity', array('class' => 'ReportePCBundle:PC',
                                             'choices' => $pcs,
                                             'property'=>'inv',
                                             'required'=>false,
                                             'label'=>'No. Inventario', 
                                            //This is deprecated because of the SimpleThingsFormExtraBundle
                                             'attr'=>array('help'=>'Si no aparece el No. Inventario debe dejar la casilla en blanco')));
        }
        //si el objeto esta siendo modificado por un admin
        else
        {
            $session = $this->request->getSession();
            $op = $session->get('op');
            
            //buscar las pcs del area del autor de la OT
            $pcs = $this->em->getRepository('ReportePCBundle:PC')->findBy(array('area' => $entity->getAutor()->getArea()->getId()),
                                                                          array('inv' => 'ASC'));
            
           if($op == 'update')
            {
                $form->add('pc', 'entity', array('class' => 'ReportePCBundle:PC','choices' => $pcs,
                                                 'property'=>'inv','required'=>false,'label'=>'No. Inv'));
            }
            elseif($op == 'solve')
            {
                $form->add('labor_realizada', null, array('attr'=>array('class'=>'ckeditor'),'required'=>true)); 
                $form->add('piezas', null, array('attr'=>array('class'=>'ckeditor')));
                $form->add('observacion', null, array('attr'=>array('class'=>'ckeditor')));
                $form->add('piezas_recuperadas');
                $form->add('fecha_solucion', null, array('required'=>true,'data' => new \DateTime('today')));
                $form->add('pc', 'entity', array('class' => 'ReportePCBundle:PC','choices' => $pcs,
                                                 'property'=>'inv','required'=>true,'label'=>'No. Inventario'));
                //buscar tecnicos
                $tecnicos = $this->em->getRepository('ReporteUserBundle:User')->findTecnicos();
                $form->add('tecnico', 'entity', array('class' => 'ReporteUserBundle:User',
                                                      'choices' => $tecnicos,
                                                      'property'=>'username',
                                                      'required'=>true));
            }
        }
        
        $form->add('submit', 'submit', array('label' => 'Aceptar','attr'=>array('class'=>'btn btn-primary')));
    }

}

?>
