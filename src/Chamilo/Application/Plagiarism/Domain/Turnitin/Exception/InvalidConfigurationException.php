<?php

namespace Chamilo\Application\Plagiarism\Domain\Turnitin\Exception;

use Chamilo\Application\Plagiarism\PlagiarismException;

/**
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin\Exception
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class InvalidConfigurationException extends PlagiarismException
{

    /**
     * MalformedRequestException constructor.
     */
    public function __construct()
    {
        parent::__construct('The Turnitin configuration is not valid. Either the settings are invalid or the webhook has not been installed. The system can not proceed without either.');
    }
}