<?php

namespace App\Form;

use App\Entity\Clients;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            #->add('roles') A GARDER
            ->add('password', PasswordType::class, [
                'mapped'   => false,
                'required' => $options['is_new'],
            ])
            ->add('origin',HiddenType::class, [
                'mapped' => false,
                'data' => $options['origin'], //Ajoute l'origine du formulaire, s'il vient de Interventions/new notamment
            ])
            #->add('phone')
            #->add('address')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Clients::class,
            'is_new'     => true,
            'origin' => null,
        ]);
    }
}
