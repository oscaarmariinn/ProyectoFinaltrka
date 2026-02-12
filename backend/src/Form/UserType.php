<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'] ?? false;

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Administrador' => 'ROLE_ADMIN',
                    'Usuario' => 'ROLE_USER',
                ],
                'multiple' => false,
                'expanded' => false,
                'mapped' => false,
                'label' => 'Rol',
                'data' => !empty($options['data']->getRoles()) ? $options['data']->getRoles()[0] : 'ROLE_USER',
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'required' => !$isEdit,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => $isEdit ? [] : [
                    new NotBlank([
                        'message' => 'Por favor introduce una contraseña',
                    ]),
                ],
            ])
            ->add('name')
            ->add('surname')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);
    }
}
