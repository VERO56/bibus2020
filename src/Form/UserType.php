<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isActive', CheckboxType::class, [
                'label_attr' => ['class' => 'switch-custom'],
                'label'=> 'Actif/Inactif',
                'required' => false,
            ])
            ->add('picture', FileType::class, [
                'label'=> 'Photo',
                'required' => false,
                'mapped' => false
            ])
            ->add('identifier', TextType::class, [
                'label' => 'Matricule'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email de contact'
            ])
            ->add('function', TextType::class, [
                'label' => 'Fonction',
                'required' => false,
            ])
            ->add('roles', ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'label' => 'Rôles',
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Utilisateur' => 'ROLE_USER'
                    // ...
                ],
            ])
            ->add('password', PasswordType::class, [
                'empty_data' => '',
                'label' => 'Mot de passe',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'empty_data' => function (FormInterface $form) {
                return new User($form->get('password')->getData());
            },
        ]);
    }
}
