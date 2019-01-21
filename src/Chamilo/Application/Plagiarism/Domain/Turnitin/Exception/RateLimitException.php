<?php

namespace Chamilo\Application\Plagiarism\Domain\Turnitin\Exception;

/**
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin\Exception
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RateLimitException extends \Exception
{
    /**
     * RateLimitException constructor.
     */
    public function __construct()
    {
        parent::__construct('Request has been rejected due to rate limiting');
    }
}