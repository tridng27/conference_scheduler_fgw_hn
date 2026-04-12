<?php

namespace App\Form;

use App\Entity\Room;
use App\Entity\Session;
use App\Entity\Venue;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('capacity', IntegerType::class)
            ->add('building')
            ->add('floor')
            ->add('equipment', TextType::class, [
                'required' => false,
            ])
            ->add('sessions', EntityType::class, [
                'class' => Session::class,
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
            ])
            ->add('venues', EntityType::class, [
                'class' => Venue::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}
