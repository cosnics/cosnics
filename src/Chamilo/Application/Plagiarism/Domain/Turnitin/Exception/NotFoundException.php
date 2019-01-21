<?php

namespace Chamilo\Application\Plagiarism\Domain\Turnitin\Exception;

/**
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin\Exception
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotFoundException extends \Exception
{
    /**
     * NotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct('The resource you where looking for could not be found');
    }
}