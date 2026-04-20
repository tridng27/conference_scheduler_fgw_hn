<?php

namespace App\Form;

use App\Entity\Conference;
use App\Entity\Room;
use App\Entity\Session;
use App\Entity\Speaker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('startTime', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('endTime', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('maxAttendees', IntegerType::class)
            ->add('sessionType')
            ->add('status')
            ->add('track', TextType::class, [
                'required' => false,
            ])
            ->add('capacity', IntegerType::class, [
                'required' => false,
            ])
            ->add('conference', EntityType::class, [
                'class' => Conference::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose conference',
                'required' => true,
            ])
            ->add('speakers', EntityType::class, [
                'class' => Speaker::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
            ])
            ->add('rooms', EntityType::class, [
                'class' => Room::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
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
