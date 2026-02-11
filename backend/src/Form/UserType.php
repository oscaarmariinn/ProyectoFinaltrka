<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Group;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('name')
            ->add('surname')
            ->add('createdAt', null, [
                'widget' => 'single_text'
            ])
            ->add('active')
            ->add('userGroups', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('attendingEvents', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('responsibleGroups', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
