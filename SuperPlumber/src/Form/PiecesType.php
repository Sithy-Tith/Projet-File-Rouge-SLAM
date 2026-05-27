<?php

namespace App\Form;

use App\Entity\Pieces;
use App\Entity\UsedPieces;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PiecesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom *',
                'attr' => [
                    'placeholder' => 'Nom de la pièce ...'
                ]
            ])
            ->add('quantity', null, [
                'label' => 'Quantité *',
                'attr' => [
                    'placeholder' => 'Quantité en stock ...'
                ]
            ])
            ->add('alertTreshold', null, [
                'label' => "Seuil d'alerte *",
                'attr' => [
                    'placeholder' => "Seuil d'alerte ..."
                ]
            ])
            ->add('supplier', null, [
                'label' => 'Fournisseur *',
                'attr' => [
                    'placeholder' => 'Fournisseur ...'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pieces::class,
        ]);
    }
}
