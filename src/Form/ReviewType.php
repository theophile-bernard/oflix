<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                "label" => 'Nom d\'utilisateur'
            ])
            ->add('email', EmailType::class, [
                "label" => 'Email de l\'utilisateur'
            ])
            ->add('content', TextareaType::class, [
                "label" => 'Texte de votre critique'
            ])
            ->add('rating', ChoiceType::class, [
                'placeholder' => 'Choisissez une option',
                'placeholder_attr' => [
                    'title' => 'Choisissez une option',
                    'disabled' => 'disabled'
                ],
                'choices'  => [
                    'Excellent' => 5,
                    'Très bon' => 4,
                    'Bon' => 3,
                    'Peut mieux faire' => 2,
                    'A éviter' => 1,
                ],
                'expanded' => false,
                'multiple' => false,
                'label' => 'Votre appréciation'
            ])
            ->add('reactions', ChoiceType::class, [
                'choices'  => [
                    'Rire' => 'smile',
                    'Pleurer' => 'cry',
                    'Réfléchir' => 'think',
                    'Dormir' => 'sleep',
                    'Rêver' => 'dream',
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => "Ce film vous à fait"
            ])
            ->add('watchedAt', DateType::class, [
                'label' => 'Quand avez vous vu ce film',
                "widget" => "single_text",
                "empty_data" => (new \DateTimeImmutable())->format('d/m/Y')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
