<?php

namespace Umpirsky\UpgradeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationFormType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'by_reference' => false,
        ]);

        $this->setConverterNormalizer($resolver);
    }

    protected function setConverterNormalizer(OptionsResolverInterface $resolver)
    {
        $resolver->setNormalizers([
            'converter' => function (Options $options, $value) use ($searchRegistry) {
                return $searchRegistry->getSearchHandler($options['autocomplete_alias']);
            }
        ]);
    }
}
