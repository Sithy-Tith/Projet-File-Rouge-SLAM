<?php

namespace App\Form;

use App\Entity\Clients;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', null, [
                'label' => 'Nom *',
                'attr' => [
                    'placeholder' => 'Dupont'
                ],
                'disabled' => $options['from_client'],
            ])
            ->add('firstName', null, [
                'label' => 'Prénom *',
                'attr' => [
                    'placeholder' => 'Jean'
                ],
                'disabled' => $options['from_client'],
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
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => [
                    'class' => 'adresse-autocomplete',
                    'autocomplete' => 'off'
                ]
            ])

            #->add('roles') A GARDER
            ->add('password', PasswordType::class, [
                'mapped'   => false,
                'required' => $options['is_new'],
                'label' => "Mot de passe",
                'attr' => [
                    'placeholder' => $options['is_new'] ? 'Créer un mot de passe' : 'Nouveau mot de passe',
                ]
            ])
            ->add('origin', HiddenType::class, [
                'mapped' => false,
                'data' => $options['origin'], //Ajoute l'origine du formulaire, s'il vient de Interventions/new notamment
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Clients::class,
            'is_new'     => true,
            'origin' => null,
            'from_client' => false,
        ]);
    }
}
