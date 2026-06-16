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

class InterventionsPlumberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fkClient', EntityType::class, [
                'class' => Clients::class,
                'choice_label' => 'fullName',
                'label' => 'Client',
                'disabled' => true,
            ])
            ->add('type', EnumType::class, [
                'class' => Type::class,
                'label' => "Type d'intervention *",
            ])
            ->add('startAt', null, [
                'required' => false,
                'label' => "Commence le :",
                'disabled' => true,
            ])
            ->add('endAt', null, [
                'required' => false,
                'label' => "Termine le :",
                'disabled' => true,
            ])
            ->add('description', null, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description... (facultatif)',
                ]
            ])
            ->add('status', EnumType::class, [
                'class' => Status::class,
                'label' => "Statut *",
                'choice_label' => fn(Status $status) => $status->label(), #On affiche la traduction de la value de l'enum
            ])
            /*
            ->add('fkEmployee', EntityType::class, [
                'class' => Employees::class,
                'choice_label' => 'fullName',
                'required' => false,
                'label' => 'Employé',
                'placeholder' => 'Aucun plombier assigné',
            ])
            */

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Interventions::class,
        ]);
    }
}
