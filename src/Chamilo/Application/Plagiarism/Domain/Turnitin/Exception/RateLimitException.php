<?php

namespace Chamilo\Application\Plagiarism\Domain\Turnitin\Exception;

use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;

/**
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin\Exception
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RateLimitException extends PlagiarismException
{
    /**
     * RateLimitException constructor.
     */
    public function __construct()
    {
        parent::__construct('Request has been rejected due to rate limiting');
    }
}