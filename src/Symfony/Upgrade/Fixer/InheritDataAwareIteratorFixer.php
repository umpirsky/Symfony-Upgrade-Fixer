<?php

namespace Symfony\Upgrade\Fixer;

class InheritDataAwareIteratorFixer extends RenameClassFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        return $this->rename(
            $content,
            ['Symfony', 'Component', 'Form', 'Util', 'VirtualFormAwareIterator'],
            'InheritDataAwareIterator'
        );
    }

    public function getDescription()
    {
        return 'The class VirtualFormAwareIterator was renamed to InheritDataAwareIterator.';
    }
}
