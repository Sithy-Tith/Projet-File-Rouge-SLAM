<?php

namespace App\Form;

use App\Entity\Employees;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\CallbackTransformer; #Pour la transformation en string

class EmployeesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            #->add('roles') A GARDER
            ->add('password')
            ->add('lastName')
            ->add('firstName')
            ->add('phone')
            ->add('position')
        ;
        $builder->add('roles', ChoiceType::class, [
            'choices' => [
                'Utilisateur' => 'ROLE_USER',
                'Plombier' => 'ROLE_PLUMBER',
                'Administrateur' => 'ROLE_ADMIN'
            ],
            'expanded' => false,
            'multiple' => false,
            'label' => 'Rôle'
        ]);
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    return count($rolesArray) ? $rolesArray[0] : null; // on vérifie s'il y a au moins un rôle. Si oui, on extrait le premier élément
                },
                function ($rolesString) {
                    return [$rolesString]; // enregistrement vers base de données
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employees::class,
        ]);
    }
}
