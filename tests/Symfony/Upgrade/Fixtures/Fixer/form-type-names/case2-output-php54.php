<?php

namespace Umpirsky\UpgradeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', 'Symfony\Component\Form\Extension\Core\Type\TextType')
        ;
    }
}
