<?php

namespace App\Form;

use App\Entity\Conference;
use App\Entity\Registration;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConferenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('location')
            ->add('startDate')
            ->add('endDate')
            ->add('status')
            ->add('createAt')
            ->add('maxAttendees')
            ->add('isActive')
            ->add('createdAt')
            ->add('organizer', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('registrations', EntityType::class, [
                'class' => Registration::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Conference::class,
        ]);
    }
}
