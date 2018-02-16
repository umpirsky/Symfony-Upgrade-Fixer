<?php

namespace Umpirsky\UpgradeBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\AbstractType;

class TextType extends AbstractType
{
    public function getParent()
    {
        return HiddenType::class;
    }
}
