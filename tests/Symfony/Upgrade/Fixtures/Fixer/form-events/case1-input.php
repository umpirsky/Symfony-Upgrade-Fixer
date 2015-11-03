<?php

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

$builder->addEventListener(FormEvents::PRE_BIND, function (FormEvent $event) {
   // ...
});
