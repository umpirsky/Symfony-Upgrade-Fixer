<?php

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

$builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
   // ...
});
