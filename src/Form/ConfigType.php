<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Config;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Formulaire d'import du fichier CSV des utilisateurs
 *
 * 
 */
class ConfigType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', TextType::class, array('required' => false))
        ->add('value', TextType::class, array('required' => false));
        //->add('Save config', 'submit', array('attr' => array('class' => 'btn btn-primary')));
    }

    public function getName()
    {
        return 'config';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Config::class,
            
        ]);
    }
}