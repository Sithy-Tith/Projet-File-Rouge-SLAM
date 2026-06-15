<?php

namespace App\Form;

use App\Entity\Employees;
use App\Enum\Position;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeesSelfType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', null, [
                'label' => 'Nom *',
                'attr' => [
                    'placeholder' => 'Dupont'
                ],
                'disabled' => true,
            ])
            ->add('firstName', null, [
                'label' => 'Prénom *',
                'attr' => [
                    'placeholder' => 'Jean'
                ],
                'disabled' => true,
            ])
            ->add('position', EnumType::class, [
                'class' => Position::class,
                'label' => "Poste *",
                'choice_label' => fn(Position $position) => $position->label(), #On affiche la traduction de la value de l'enum
                'disabled' => true,
            ])
            ->add('email', null, [
                'label' => 'Adresse email *',
                'attr' => [
                    'placeholder' => 'jean.dupont@exemple.com'
                ]
            ])
            ->add('phone', null, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => '06xxxxxxxx'
                ]
            ])
            ->add('password', PasswordType::class, [
                'mapped'   => false,
                'required' => $options['is_new'],
                'label' => "Mot de passe",
                'attr' => [
                    'placeholder' => $options['is_new'] ? '' : 'Nouveau mot de passe',
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employees::class,
            'is_new'     => true,
        ]);
    }
}
