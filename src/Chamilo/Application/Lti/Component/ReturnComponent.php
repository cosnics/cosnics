<?php
namespace Chamilo\Application\Lti\Component;

use Chamilo\Application\Lti\Manager;

/**
 * @package Chamilo\Application\Lti\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ReturnComponent extends Manager
{

    /**
     *
     * @return string
     */
    function run()
    {
        var_dump($this->getRequest());
    }
}