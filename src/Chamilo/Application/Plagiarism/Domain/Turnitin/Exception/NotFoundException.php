<?php

namespace Chamilo\Application\Plagiarism\Domain\Turnitin\Exception;

use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;

/**
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin\Exception
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotFoundException extends PlagiarismException
{
    /**
     * NotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct('The resource you where looking for could not be found');
    }
}