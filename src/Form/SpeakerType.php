<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Speaker;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpeakerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('email')
            ->add('company')
            ->add('jobTitle')
            ->add('bio')
            ->add('expertise')
            ->add('photo')
            ->add('socialLinks')
            ->add('usser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('sessions', EntityType::class, [
                'class' => Session::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Speaker::class,
        ]);
    }
}
