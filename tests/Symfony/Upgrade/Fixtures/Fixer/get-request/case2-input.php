<?php

namespace Umpirsky\UpgradeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DemoController extends Controller
{
    public function showAction()
    {
        $request = $this->getRequest();
        // ...
    }

    private function helperMethod()
    {
        // ...
    }
}
