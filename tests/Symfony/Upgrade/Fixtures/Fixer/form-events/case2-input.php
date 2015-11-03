<?php

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

$builder->addEventListener(FormEvents::BIND, function (FormEvent $event) {
   // ...
});
