<?php

namespace App\Form;

use App\Entity\conference;
use App\Entity\room;
use App\Entity\Session;
use App\Entity\speaker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('startTime')
            ->add('endTime')
            ->add('maxAttendees')
            ->add('sessionType')
            ->add('status')
            ->add('track')
            ->add('capacity')
            ->add('conference', EntityType::class, [
                'class' => conference::class,
                'choice_label' => 'id',
            ])
            ->add('speaker', EntityType::class, [
                'class' => speaker::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('room', EntityType::class, [
                'class' => room::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
