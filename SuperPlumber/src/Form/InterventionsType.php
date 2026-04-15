<?php

namespace App\Form;

use App\Entity\Clients;
use App\Entity\Employees;
use App\Entity\Interventions;
use App\Entity\UsedPieces;
use App\Enum\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterventionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date')
            ->add('type', EnumType::class, [
                'class' => Type::class,
                'label' => "Type d'intervention"
            ])
            ->add('description')
            ->add('status')
            ->add('duration')
            ->add('fkEmployee', EntityType::class, [
                'class' => Employees::class,
                'choice_label' => 'id',
            ])
            ->add('fkClient', EntityType::class, [
                'class' => Clients::class,
                'choice_label' => 'id',
            ])
            ->add('usedPieces', EntityType::class, [
                'class' => UsedPieces::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Interventions::class,
        ]);
    }
}
