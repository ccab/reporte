<?php

namespace Reporte\UsersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;

class UserType extends AbstractType
{
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
        $builder->addEventSubscriber(new UserTypeSubscriber($this->securityContext,
                                                            $this->requestStack->getCurrentRequest(), 
                                                            $this->em)); 
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Reporte\UsersBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reporte_usersbundle_user';
    }
}
