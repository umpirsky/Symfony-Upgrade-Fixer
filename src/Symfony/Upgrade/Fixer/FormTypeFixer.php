<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

abstract class FormTypeFixer extends AbstractFixer
{
    const PREFIX_SYMFONY_TYPE = 'Symfony';

    protected $types = [
        'Birthday',
        'Button',
        'Checkbox',
        'Choice',
        'Collection',
        'Country',
        'Currency',
        'DateTime',
        'Date',
        'Email',
        'File',
        'Form',
        'Hidden',
        'Integer',
        'Language',
        'Locale',
        'Money',
        'Number',
        'Password',
        'Percent',
        'Radio',
        'Range',
        'Repeated',
        'Reset',
        'Search',
        'Submit',
        'Text',
        'Textarea',
        'Time',
        'Timezone',
        'Url',
    ];

    protected function isFormType(Tokens $tokens)
    {
        return $this->extendsClass($tokens, ['Symfony', 'Component', 'Form', 'AbstractType']);
    }

    protected function getCurrentTypeClass(Tokens $tokens)
    {
        $tokens = $this->extendsClass($tokens, ['Symfony', 'Component', 'Form', 'AbstractType']);

        $classMet = false;
        /** @var Token $token */
        foreach ($tokens as $token) {
            if (!$classMet) {
                $classMet = $token->getId() === T_CLASS;
                continue;
            }

            if (!$token->isEmpty()) {
                return $token->getContent();
            }
        }

        return null;
    }

    protected function addTypeUse(Tokens $tokens, $name, $alias = null)
    {
        $this->addUseStatement(
            $tokens,
            ['Symfony', 'Component', 'Form', 'Extension', 'Core', 'Type', $this->getTypeName($name)],
            $alias
        );
    }

    protected function getTypeName($name)
    {
        return ucfirst($name).'Type';
    }

    protected function getTypeClassAlias($type, $currentClassName)
    {
        $typeClass = $this->getTypeName($type);
        if ($typeClass === $currentClassName) {
            return self::PREFIX_SYMFONY_TYPE.$typeClass;
        }

        return null;
    }
}
