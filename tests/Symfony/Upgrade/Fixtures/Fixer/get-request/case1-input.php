<?php

namespace Umpirsky\UpgradeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DemoController extends Controller
{
    public function showAction($slug)
    {
        $request = $this->getRequest();
        // ...
    }
}
