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
                'label' => 'Client *',
            ])
            ->add('type', EnumType::class, [
                'class' => Type::class,
                'label' => "Type d'intervention *",
            ])
            ->add('description', null, [
                'label' => 'Description *',
                'attr' => [
                    'placeholder' => 'Description...',
                ]
            ])
            ->add('startAt', null, [
                'required' => false,
                'label' => "Commence le :"
            ])
            ->add('endAt', null, [
                'required' => false,
                'label' => "Termine le :",
            ])
            ->add('description', null, [
                'required' => false,
                'label' => 'Description (facultatif)',
            ])
            ->add('status', EnumType::class, [
                'class' => Status::class,
                'label' => "Statut *",
                'data' => Status::TO_PLAN,
                'choice_label' => fn(Status $status) => $status->label(), #On affiche la traduction de la value de l'enum
            ])
            ->add('fkEmployee', EntityType::class, [
                'class' => Employees::class,
                'choice_label' => 'fullName',
                'label' => 'Plombier en charge',

                'required' => false,
                'placeholder' => 'Aucun plombier assigné',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Interventions::class,
        ]);
    }
}
