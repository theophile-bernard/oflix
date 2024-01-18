<?php

namespace App\Form;

use App\Entity\Movie;
use App\Entity\Person;
use App\Entity\Casting;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CastingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('role', TextType::class, [
                "label" => "Rôle"
            ])
            ->add('castingOrder', IntegerType::class, [
                "label" => "Ordre d'Apparition"
            ])
            ->add('movie', EntityType::class, [
                'class' => Movie::class,
                'choice_label' => 'title',
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('m')
                        ->orderBy('m.title', 'ASC');
                },
            ])
            ->add('person', EntityType::class, [
                'class' => Person::class,
                'choice_label' => function (Person $person): string {
                    return $person->getDisplayName();
                },
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.firstname', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Casting::class,
        ]);
    }
}
