<?php

namespace App\Form;

use App\Entity\TourPersonnalise;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TourPersonnaliseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('duration', null, [
                'label' => 'Duration (hours)',
            ])
            ->add('price')
            ->add('maxPersons')
            ->add('places', EntityType::class, [
                'class' => \App\Entity\Place::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true, // Checkboxes for multiple selection
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TourPersonnalise::class,
        ]);
    }
}
