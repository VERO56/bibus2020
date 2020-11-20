<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Formulaire d'import du fichier CSV des utilisateurs
 */
class CsvImportType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('csvfile', FileType::class)
            ->add('import', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
    }

    public function getName()
    {
        return 'csvimport';
    }
}