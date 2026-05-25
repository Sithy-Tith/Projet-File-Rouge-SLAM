<?php

namespace App\Form;

use App\Entity\Clients;
use App\Entity\Employees;
use App\Entity\Interventions;
use App\Entity\UsedPieces;
use App\Enum\Type;
use App\Enum\Status;
use PhpParser\Node\Stmt\Label;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterventionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fkClient', EntityType::class, [
                'class' => Clients::class,
                'choice_label' => 'fullName',
                'placeholder' => 'Choix du client',
                'label' => 'Client',
            ])
            ->add('type', EnumType::class, [
                'class' => Type::class,
                'label' => "Type d'intervention",
            ])
            ->add('description')
            ->add('duration', null, [
                'label' => "Durée estimée",
            ])
            ->add('status', EnumType::class, [
                'class' => Status::class,
                'label' => "Statut",
                'data' => Status::TO_PLAN,
            ])
            ->add('date', null, [
                'required' => false,
            ])
            ->add('fkEmployee', EntityType::class, [
                'class' => Employees::class,
                'choice_label' => 'fullName',
                'label' => 'Plombier en charge',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Interventions::class,
        ]);
    }
}
