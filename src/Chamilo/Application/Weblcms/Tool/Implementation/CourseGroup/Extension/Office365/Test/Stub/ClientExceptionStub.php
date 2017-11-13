<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Test\Stub;

use GuzzleHttp\Exception\ClientException;

/**
 * Stub for the ClientException
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Test\Stub
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ClientExceptionStub extends ClientException
{
    public function __construct($code)
    {
        \Exception::__construct('test', $code);
    }
}