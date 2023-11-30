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
            ->add('name')
            ->add('description')
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
            ->add('releaseDate')
            ->add('nbPages')
            ->add('ISBN')
            ->add('enabled')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
