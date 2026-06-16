<?php

namespace App\Form;

use App\Entity\Availabilities;
use App\Entity\Employees;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvailabilitiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start', DateTimeType::class, [
                'date_widget' => 'single_text',
                'time_widget' => 'choice',
                'hours' => range(8, 18),
                'minutes' => [0, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55],
                'label' => 'Début *',
                'required' => true,
                'placeholder' => [
                    'hour' => 'Heure',
                    'minute' => 'Minute',
                ],
            ])
            ->add('end', DateTimeType::class, [
                'date_widget' => 'single_text',
                'time_widget' => 'choice',
                'hours' => range(8,18),
                'minutes' => [0, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55],
                'label' => 'Fin *',
                'required' => true,
                'placeholder' => [
                    'hour' => 'Heure',
                    'minute' => 'Minute',
                ],
            ])

            ->add('fkEmployee', EntityType::class, [
                'class' => Employees::class,
                'choice_label' => 'fullName',
                'placeholder' => 'Choix du plombier',
                'label' => 'Plombier *',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Availabilities::class,
        ]);
    }
}
