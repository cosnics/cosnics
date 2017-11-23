<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Stub;

use GuzzleHttp\Exception\ClientException;

/**
 * Stub for the ClientException
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Stub
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