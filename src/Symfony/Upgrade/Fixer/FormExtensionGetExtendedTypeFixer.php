<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class FormExtensionGetExtendedTypeFixer extends FormExtensionFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        if ($this->isFormType($tokens)) {
            foreach ($this->types as $type) {
                if (null === $this->matchGetExtendedType($tokens, $type)) {
                    continue;
                }

                $this->addTypeUse($tokens, $type);
                $this->fixGetExtendedTypeMethod($tokens, $type);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * @param Tokens $tokens
     * @param string $name
     * @return Token[]|null
     */
    private function matchGetExtendedType(Tokens $tokens, $name)
    {
        return $tokens->findSequence([
            [T_PUBLIC, 'public'],
            [T_FUNCTION],
            [T_STRING, 'getExtendedType'],
            '(',
            ')',
            '{',
            [T_RETURN],
            [T_CONSTANT_ENCAPSED_STRING, sprintf("'%s'", strtolower($name))],
            ';',
            '}',
        ]);
    }

    /**
     * @param Tokens $tokens
     * @param string $name
     */
    private function fixGetExtendedTypeMethod(Tokens $tokens, $name)
    {
        $matchedTokens = $this->matchGetExtendedType($tokens, $name);
        if (null === $matchedTokens) {
            return;
        }

        $matchedIndexes = array_keys($matchedTokens);

        $matchedIndex = $matchedIndexes[count($matchedIndexes) - 3];

        $tokens->insertAt(
            $matchedIndex,
            [
                new Token([T_STRING, $name.'Type']),
                new Token([T_DOUBLE_COLON, '::']),
            ]
        );
        $matchedTokens[$matchedIndex]->override([CT_CLASS_CONSTANT, 'class']);

        $this->fixGetExtendedTypeMethod($tokens, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Type extension must return the fully-qualified class name of the extended type from FormTypeExtensionInterface::getExtendedType() now.';
    }
}
