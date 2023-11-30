<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name' , null, [
                'label' => 'Nom du livre',
                'attr' => [
                    'placeholder' => 'Nom du livre'
                ]
            ])
            ->add('description', null, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description'
                ]
            ])
            ->add('author', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'fullname',
                'multiple' => false,
                'expanded' => false,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('a')
                        ->where('a.enabled = :enabled')
                        ->setParameter('enabled', true)
                        ->orderBy('a.lastname', 'ASC')
                        ;
                },
                'autocomplete' => true,
                'required' => true,
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'label',
                'multiple' => true,
                'expanded' => false,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('c')
                        ->where('c.enabled = :enabled')
                        ->setParameter('enabled', true)
                        ->orderBy('c.label', 'ASC')
                        ;
                },
                'autocomplete' => true,
                'by_reference' => false,
                'required' => false,
            ])
            ->add('releaseDate', null, [
                'label' => 'Date de sortie',
                'attr' => [
                    'placeholder' => 'Date de sortie'
                ],
                'widget' => 'single_text'
            ])
            ->add('nbPages', null, [
                'label' => 'Nombre de pages',
                'attr' => [
                    'placeholder' => 'Nombre de pages'
                ]
            ])
            ->add('ISBN', null, [
                'label' => 'ISBN',
                'attr' => [
                    'placeholder' => 'ISBN'
                ]
            ])
            ->add('enabled', null, [
                'label' => 'Activer le livre',
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
