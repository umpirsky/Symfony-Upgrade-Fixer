<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class FormOptionNamesFixer extends FormTypeFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        if ($this->isFormType($tokens)) {
            $fieldNameTokenSets = [
                [[T_CONSTANT_ENCAPSED_STRING]],
                [[T_STRING], [T_DOUBLE_COLON], [CT_CLASS_CONSTANT]],
            ];

            foreach ($fieldNameTokenSets as $fieldNameTokens) {
                $fieldNames = [
                    'precision' => 'scale',
                    'virtual' => 'inherit_data',
                ];

                foreach ($fieldNames as $oldName => $newName) {
                    $this->fixOptionNames($tokens, $fieldNameTokens, $oldName, $newName);
                }
            }
        }

        return $tokens->generateCode();
    }

    public function getDescription()
    {
        return 'Options precision and virtual was renamed to scale and inherit_data.';
    }

    private function fixOptionNames(Tokens $tokens, $fieldNameTokens, $oldName, $newName, $start = 0)
    {
        $matchedTokens = $tokens->findSequence(array_merge(
            [
                [T_OBJECT_OPERATOR],
                [T_STRING, 'add'],
                '(',
                [T_CONSTANT_ENCAPSED_STRING],
                ',',
            ],
            $fieldNameTokens,
            [',']
        ), $start);

        if (null === $matchedTokens) {
            return;
        }

        $matchedTokenIndexes = array_keys($matchedTokens);
        $isArray = $tokens->isArray(
            $index = $tokens->getNextMeaningfulToken(end($matchedTokenIndexes))
        );

        if (!$isArray) {
            return;
        }

        do {
            $index = $tokens->getNextMeaningfulToken($index);
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
                continue;
            }

            if ("'$oldName'" === $token->getContent()) {
                $token->setContent("'$newName'");
            }
        } while (!in_array($token->getContent(), [')', ']']) );

        $this->fixOptionNames($tokens, $fieldNameTokens, $oldName, $newName, $index);
    }
}
