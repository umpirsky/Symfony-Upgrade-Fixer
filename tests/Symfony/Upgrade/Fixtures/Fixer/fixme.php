<?php

use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\PropertyAccess\PropertyAccess;

$progress = new ProgressHelper($output, 50);
$accessor = PropertyAccess::getPropertyAccessor();
