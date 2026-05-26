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
                'widget' => 'single_text',
                'label' => 'Début',
            ])
            ->add('end', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Fin',
            ])
            ->add('all_day', null, [
                'label' => 'Toute la journée',
                'required' => false,
            ])
            ->add('fkEmployee', EntityType::class, [
                'class' => Employees::class,
                'choice_label' => 'fullName',
                'placeholder' => 'Choix du plombier',
                'label' => 'Plombier',
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
