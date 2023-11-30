<?php

namespace App\Form;

use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname' , null, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Prénom'
                ]
            ])
            ->add('lastname' , null, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Nom'
                ]
            ])
            ->add('biography', null, [
                'label' => 'Biographie',
                'attr' => [
                    'placeholder' => 'Biographie'
                ]
            ])
            ->add('birthDate', null, [
                'label' => 'Date de naissance',
                'attr' => [
                    'placeholder' => 'Date de naissance'
                ],
                'widget' => 'single_text'
            ])
            ->add('origin', null, [
                'label' => 'Origine',
                'attr' => [
                    'placeholder' => 'Origine'
                ]
            ])
            ->add('enabled', null, [
                'label' => 'Activer l\'auteur',
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }
}
