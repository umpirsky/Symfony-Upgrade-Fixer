<?php

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

$builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
   // ...
});
