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
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'log2.txt', var_export($this->getRequest(), true));
    }
}