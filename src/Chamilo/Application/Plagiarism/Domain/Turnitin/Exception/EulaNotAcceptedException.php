<?php

namespace Chamilo\Application\Plagiarism\Domain\Turnitin\Exception;

use Chamilo\Application\Plagiarism\PlagiarismException;

/**
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin\Exception
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EulaNotAcceptedException extends PlagiarismException
{

    /**
     * MalformedRequestException constructor.
     */
    public function __construct()
    {
        parent::__construct('The user has not accepted the EULA and must be redirect to the accept EULA component');
    }
}