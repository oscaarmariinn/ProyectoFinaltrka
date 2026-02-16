<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\EventType;
use App\Entity\Group;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('eventDate', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('location')
            ->add('maxParticipants')
            ->add('isPublic')

            ->add('eventType', EntityType::class, [
                'class' => EventType::class,
                'choice_label' => 'name',
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
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
