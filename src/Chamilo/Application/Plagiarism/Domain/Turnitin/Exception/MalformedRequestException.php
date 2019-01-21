<?php

namespace Chamilo\Application\Plagiarism\Domain\Turnitin\Exception;

/**
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin\Exception
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MalformedRequestException extends \Exception
{

    /**
     * MalformedRequestException constructor.
     */
    public function __construct()
    {
        parent::__construct('Request is malformed or missing required data');
    }
}