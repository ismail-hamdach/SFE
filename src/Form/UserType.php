<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom', null, [
                'label' => 'Prénom'
            ])
            ->add('email', EmailType::class)
            ->add('roles', ChoiceType::class, [
                'label' => 'Type',
                'choices' => ['Admin' => 'ROLE_ADMIN', 'Gérant' => 'ROLE_GERANT', 'Employé' => 'ROLE_EMPLOYE'],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('password', null, [
                'label' => 'Mot de passe',
            ])
            ->add('service')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
