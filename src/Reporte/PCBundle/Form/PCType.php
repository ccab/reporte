<?php

namespace Reporte\PCBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PCType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('inv',null,array('label'=>'No. Inv'))
            ->add('marca',null,array('property'=>'nombre'))
            ->add('area',null,array('property'=>'nombre'))
            ->add('esLaptop',null,array('required'=>false))
            ->add('submit', 'submit', array('label' => 'Aceptar','attr'=>array('class'=>'form-submit')));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Reporte\PCBundle\Entity\PC'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reporte_pcbundle_pc';
    }
}
