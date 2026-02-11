<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\EventType;
use App\Entity\Group;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('eventDate')
            ->add('location')
            ->add('maxParticipants')
            ->add('isPublic')
            ->add('createdAt', null, [
                'widget' => 'single_text'
            ])
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('eventGroup', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'id',
            ])
            ->add('eventType', EntityType::class, [
                'class' => EventTypeForm::class,
                'choice_label' => 'id',
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'id',
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
