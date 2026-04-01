<?php

namespace App\Form;

use App\Entity\Interventions;
use App\Entity\Pieces;
use App\Entity\UsedPieces;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsedPiecesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isConsumable')
            ->add('fkIntervention', EntityType::class, [
                'class' => Interventions::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('fkPiece', EntityType::class, [
                'class' => Pieces::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UsedPieces::class,
        ]);
    }
}
