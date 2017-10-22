<?php

namespace Umpirsky\UpgradeBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;

class RegistrationFormExtension extends AbstractTypeExtension
{
    public function getExtendedType()
    {
        return 'form';
    }
}
