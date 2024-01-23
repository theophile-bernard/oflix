<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{

    public function __construct(
        private UserRepository $userRepository,
    ) {}
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                "empty_data" => "",
            ])
            ->add('roles', ChoiceType::class, [
                "choices" => [
                    "User" => "ROLE_USER",
                    "Manager" => "ROLE_MANAGER",
                    "Admin" => "ROLE_ADMIN"
                ],
                "empty_data" => "",
            ])
        ;

        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($tagsAsArray): string {
                    // transform the array to a string
                    return implode(', ', $tagsAsArray);
                },
                function ($tagsAsString): array {
                    // transform the string back to an array
                    return explode(', ', $tagsAsString);
                }
            ))
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $user = $event->getData();
            if($user->getId())
            {
                $event->getForm()->add('password', PasswordType::class, [
                    "empty_data" => "",
                    // REFER : https://symfony.com/doc/6.4/reference/forms/types/password.html#mapped
                    // Ce champ n'est plus mappé entre le formulaire et l'entité
                    // les modifications faites dans le formulaire ne seront pas directement répercutées dans l'entité
                    "mapped" => false,
                    'attr'          => [
                        'placeholder'   => 'Laissez vide si le mot de passe est inchangé',
                    ],
                ]);
            }
            else
            {
                $event->getForm()->add('password', PasswordType::class, [
                    "empty_data" => "",
                    // REFER : https://symfony.com/doc/6.4/reference/forms/types/password.html#mapped
                    // Ce champ n'est plus mappé entre le formulaire et l'entité
                    // les modifications faites dans le formulaire ne seront pas directement répercutées dans l'entité
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr'       => ['novalidate' => 'novalidate'],
        ]);
    }
}
