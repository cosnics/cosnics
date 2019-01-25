<?php

namespace Chamilo\Application\Plagiarism\Domain\Turnitin\Exception;

use Chamilo\Application\Plagiarism\PlagiarismException;

/**
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin\Exception
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UnexpectedErrorException extends PlagiarismException
{
    /**
     * RateLimitException constructor.
     */
    public function __construct()
    {
        parent::__construct('An unexpected error was encountered');
    }
}