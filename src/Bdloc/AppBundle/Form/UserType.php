<?php

namespace Bdloc\AppBundle\Form;

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
            ->add('username', 'text', array(
                    "label" => "Votre pseudo"
                ))
            ->add('firstName', 'text', array(
                    "label" => "Votre prénom"
                ))
            ->add('lastName', 'text', array(
                    "label" => "Votre Nom"
                ))
            ->add('email', 'email', array(
                    "label" => "Votre email"
                ))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'Les mots de passe doivent correspondre',
                'options' => array('required' => true),
                'first_options'  => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Mot de passe (validation)'),
                ))
            ->add('submit', 'submit', array(
                    "label" => "Inscription !"))
        
        ;
    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bdloc\AppBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bdloc_appbundle_user';
    }
}
