<?php

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

$builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
   // ...
});
