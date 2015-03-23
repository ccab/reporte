<?php

namespace Reporte\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',null,array('label'=>'Usuario'))
            //->add('salt')
            //->add('password')
            ->add('password', 'repeated', array(
                    'first_name' => 'clave',
                    'second_name' => 'repetir_clave',
                    'type' => 'password' ))
            ->add('email',null,array('label'=>'Correo Electronico'))
            ->add('area',null,array('property'=>'nombre','required'=>true))
            //->add('isActive')
            ->add('roles',null,array('property'=>'name','expanded'=>true,'required'=>true))
            ->add('submit', 'submit', array('label' => 'Aceptar','attr'=>array('class'=>'form-submit')));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Reporte\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'monta_userbundle_user';
    }
}
