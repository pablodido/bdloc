<?php

namespace Bdloc\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FilterBookPaginationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('page', null, array(
                'label' => "N° de page"
                ))
            ->add('orderBy', 'choice', array(
                'choices' => array('dateCreated' => "Date d'ajout au catalogue", 'title' => 'Titre'),
                'label' => "Trié par"
                ))
            ->add('orderDir', 'choice', array(
                'choices' => array('asc' => 'Croissant', 'desc' => 'Décroissant'),
                'label' => "Ordre"
                ))
            ->add('numPerPage', null, array(
                'label' => "Affichage par page"
                ))
            ->add('keywords', 'hidden')
            ->add('categories', 'hidden')
            ->add('availability', 'hidden')
            ->add('submit', 'submit', array(
                'label' => 'Filtrer'
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bdloc\AppBundle\Entity\FilterBook'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bdloc_appbundle_filterbookpagination';
    }
}
