<?php

namespace Reporte\ServicioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Reporte\ServicioBundle\Form\ServicioTypeSubscriber;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;


class ServicioType extends AbstractType{
    
    private $securityContext;
    private $requestStack;
    private $em;
     
    public function __construct(SecurityContext $securityContext, RequestStack $requestStack, EntityManager $em)
    {
        $this->securityContext = $securityContext;
        $this->requestStack = $requestStack;
        $this->em = $em;
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('usuario')
            ->add('problema', null, array('required'=>true,'attr'=>array('class'=>'ckeditor')))
            ->addEventSubscriber(new ServicioTypeSubscriber($this->securityContext,
                                                            $this->requestStack->getCurrentRequest(), 
                                                            $this->em));   
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Reporte\ServicioBundle\Entity\Servicio'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'servicio_form';
    }
}
