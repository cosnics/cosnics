<?php

namespace Chamilo\Application\Plagiarism\Domain\Turnitin\Exception;

/**
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin\Exception
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotAuthenticatedException extends \Exception
{
    /**
     * NotAuthenticatedException constructor.
     */
    public function __construct()
    {
        parent::__construct('Not Properly Authenticated');
    }
}