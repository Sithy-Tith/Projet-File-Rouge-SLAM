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
            #->add('position')
        ;
        $builder->add('roles', ChoiceType::class, [
            'choices' => [
                'Utilisateur' => 'ROLE_USER',
                'Plombier' => 'ROLE_PLUMBER',
                'Administrateur' => 'ROLE_ADMIN'
            ],
            'expanded' => false,
            'multiple' => false,
            'label' => 'Role'
        ]);
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // Transforme l'array en string pour l'affichage dans la liste
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    // Transforme le string sélectionné en array pour l'enregistrement
                    return [$rolesString];
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
