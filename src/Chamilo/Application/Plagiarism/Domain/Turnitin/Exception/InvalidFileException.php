<?php

namespace Chamilo\Application\Plagiarism\Domain\Turnitin\Exception;

use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;

/**
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin\Exception
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class InvalidFileException extends PlagiarismException
{

    /**
     * InvalidFileException constructor.
     *
     * @param string $filePath
     * @param string $filename
     */
    public function __construct(string $filePath, string $filename)
    {
        parent::__construct(
            sprintf('The given file %s (%s) is invalid and can not be checked for plagiarism', $filename, $filePath)
        );
    }
}