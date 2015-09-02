<?php

use Symfony\Component\Console\Helper\ProgressHelper;

// create a new progress bar (50 units)
$progress = new ProgressHelper($output, 50);

// start and displays the progress bar
$progress->start();

$i = 0;
while ($i++ < 50) {
    // ... do some work

    // advance the progress bar 1 unit
    $progress->advance();

    // you can also advance the progress bar by more than 1 unit
    // $progress->advance(3);
}

// ensure that the progress bar is at 100%
$progress->finish();
