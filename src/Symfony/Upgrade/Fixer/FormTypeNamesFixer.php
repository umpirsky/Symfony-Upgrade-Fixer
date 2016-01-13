<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class FormTypeNamesFixer extends FormTypeFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        if ($this->isFormType($tokens)) {
            foreach ($this->types as $type) {
                if (null === $this->matchTypeName($tokens, $type)) {
                    continue;
                }

                if (PHP_VERSION_ID < 50500) {
                    $this->fixTypeNamesLegacy($tokens, $type);
                } else {
                    $this->addTypeUse($tokens, $type);
                    $this->fixTypeNames($tokens, $type);
                }
            }
        }

        return $tokens->generateCode();
    }

    public function getDescription()
    {
        return 'Instead of referencing types by name, you should reference them by their fully-qualified class name (FQCN) instead.';
    }

    private function matchTypeName(Tokens $tokens, $name)
    {
        return $tokens->findSequence([
            [T_OBJECT_OPERATOR],
            [T_STRING, 'add'],
            '(',
            [T_CONSTANT_ENCAPSED_STRING],
            ',',
            [T_CONSTANT_ENCAPSED_STRING, sprintf("'%s'", strtolower($name))],
        ]);
    }

    private function fixTypeNames(Tokens $tokens, $name)
    {
        $matchedTokens = $this->matchTypeName($tokens, $name);
        if (null === $matchedTokens) {
            return;
        }

        $matchedIndexes = array_keys($matchedTokens);

        $matchedIndex = $matchedIndexes[count($matchedIndexes) - 1];

        $tokens->insertAt(
            $matchedIndex,
            [
                new Token([T_STRING, $name.'Type']),
                new Token([T_DOUBLE_COLON, '::']),
            ]
        );
        $matchedTokens[$matchedIndex]->override([CT_CLASS_CONSTANT, 'class']);

        $this->fixTypeNames($tokens, $name);
    }

    private function fixTypeNamesLegacy(Tokens $tokens, $name)
    {
        $matchedTokens = $this->matchTypeName($tokens, $name);
        if (null === $matchedTokens) {
            return;
        }

        $matchedIndexes = array_keys($matchedTokens);

        $matchedIndex = $matchedIndexes[count($matchedIndexes) - 1];

        $matchedTokens[$matchedIndex]->setContent('\'Symfony\\Component\\Form\\Extension\\Core\\Type\\'.$name.'Type\'');

        $this->fixTypeNamesLegacy($tokens, $name);
    }
}
