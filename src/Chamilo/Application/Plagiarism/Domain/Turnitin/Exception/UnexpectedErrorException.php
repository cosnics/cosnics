<?php

namespace Chamilo\Application\Plagiarism\Domain\Turnitin\Exception;

/**
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin\Exception
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UnexpectedErrorException extends \Exception
{
    /**
     * RateLimitException constructor.
     */
    public function __construct()
    {
        parent::__construct('An unexpected error was encountered');
    }
}