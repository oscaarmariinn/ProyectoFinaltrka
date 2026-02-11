<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Group;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Administrador' => '"ROLE_ADMIN"',
                    'Usuario' => '"ROLE_USER"',
                ],
                'multiple' => false,
                'expanded' => false,
                'mapped'   => false,
                'label' => 'Rol',

            ])
            ->add('password')
            ->add('name')
            ->add('surname')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
