<?php

namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Format\Response\DefaultComponentResponse;

class TestComponent extends Manager
{
    /**
     *
     * @return string
     */
    function run()
    {
        return new DefaultComponentResponse(
            $this, $this->getTwig()->render('Chamilo\Core\Repository:TestComponent.html.twig')
        );
    }
}