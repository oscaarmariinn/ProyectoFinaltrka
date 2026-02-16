<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Group;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Título',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Descripción',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('eventDate', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Fecha y hora',
                'required' => true,
            ])
            ->add('location', TextType::class, [
                'label' => 'Ubicación',
                'required' => true,
            ])
            ->add('maxParticipants', IntegerType::class, [
                'label' => 'Participantes máximos',
                'required' => false,
                'attr' => ['min' => 1],
            ])
            ->add('isPublic', ChoiceType::class, [
                'choices' => [
                    'Público' => '1',
                    'Privado' => '0',
                ],
                'label' => 'Privacidad del evento',
                'required' => true,
                'data' => '1', // Por defecto público
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true, // IMPORTANTE: Esto hace que sean checkboxes
                'required' => false,
                'label' => 'Categorías',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
