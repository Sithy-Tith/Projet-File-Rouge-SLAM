<?php

namespace App\Form;

use App\Entity\Employees;
use App\Enum\Position;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('position')
            ->add('password', PasswordType::class, [
                'mapped'   => false,
                'required' => $options['is_new'],
            ])
            #->add('phone')

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
