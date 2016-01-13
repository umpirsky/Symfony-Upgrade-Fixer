<?php

namespace Umpirsky\UpgradeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'Symfony\Component\Form\Extension\Core\Type\TextType', array('label' => 'form.username'))
            ->add('email', 'Symfony\Component\Form\Extension\Core\Type\EmailType', array('label' => 'form.email'))
            ->add('password', 'Symfony\Component\Form\Extension\Core\Type\PasswordType')
        ;
    }
}
