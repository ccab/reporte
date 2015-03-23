<?php

namespace Reporte\ReporteServicioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReporteServicioType extends AbstractType
{
    /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('pc_inv')
//            ->add('pc_area_nombre')
//            ->add('pc_marca_nombre')
//            ->add('es_laptop')
//            ->add('autor_username')
//            ->add('autor_area_nombre')
//            ->add('tecnico_username')
//            ->add('tecnico_area_nombre')
//            ->add('usuario')
            ->add('problema',null,array('attr'=>array('class'=>'ckeditor'),'required'=>true))
            ->add('labor_realizada',null,array('attr'=>array('class'=>'ckeditor'),'required'=>true))
//            ->add('piezas')
//            ->add('piezas_recuperadas')
//            ->add('fecha_reporte')
//            ->add('fecha_solucion')
            ->add('observacion',null,array('attr'=>array('class'=>'ckeditor'),'required'=>false))
            ->add('Aceptar','submit',array('label' => 'Aceptar','attr'=>array('class'=>'form-submit')))    
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Reporte\ReporteServicioBundle\Entity\ReporteServicio'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reporte_reporteserviciobundle_reporteservicio';
    }
}
