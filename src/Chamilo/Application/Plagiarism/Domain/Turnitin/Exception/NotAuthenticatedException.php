<?php

namespace Chamilo\Application\Plagiarism\Domain\Turnitin\Exception;

use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;

/**
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin\Exception
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotAuthenticatedException extends PlagiarismException
{
    /**
     * NotAuthenticatedException constructor.
     */
    public function __construct()
    {
        parent::__construct('Not Properly Authenticated');
    }
}