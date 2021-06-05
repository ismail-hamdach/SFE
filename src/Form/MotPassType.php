<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;


class MotPassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'required' => true,
                // 'options' => ['attr' => ['class' => 'password-field']],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les champs de nouveau mot de passe doivent correspondre.',
                // 'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Nouveau mot de passe'],
                'second_options' => ['label' => 'Répéter le nouveau mot de passe'],
            ])
        ;
    }

   
}
