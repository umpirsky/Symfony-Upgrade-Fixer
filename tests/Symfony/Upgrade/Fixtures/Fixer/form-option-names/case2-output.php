<?php

namespace Umpirsky\UpgradeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => 'form.name'))
            ->add('price1', 'text', array(
                'label' => 'form.price1',
                'scale' => 3,
            ))
            ->add('price2', TextType::class, array(
                'scale' => 3,
            ))
            ->add('discount', IntegerType::class, [
                'label' => 'form.email',
                'inherit_data' => true,
            ])
            ->add('password', PasswordType::class)
        ;
    }
}
