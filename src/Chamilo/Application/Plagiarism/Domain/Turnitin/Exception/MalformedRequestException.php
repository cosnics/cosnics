<?php

namespace Chamilo\Application\Plagiarism\Domain\Turnitin\Exception;

use Chamilo\Application\Plagiarism\PlagiarismException;

/**
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin\Exception
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MalformedRequestException extends PlagiarismException
{

    /**
     * MalformedRequestException constructor.
     */
    public function __construct()
    {
        parent::__construct('Request is malformed or missing required data');
    }
}