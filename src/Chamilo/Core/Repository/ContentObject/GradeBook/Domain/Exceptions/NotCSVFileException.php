<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions;

/**
 * Class NotCSVFileException
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class NotCSVFileException extends \Exception implements GradeBookImportException
{
    /**
     * NotCSVFileException constructor.
     */
    public function __construct()
    {
        parent::__construct('The uploaded file does not appear to be a csv file.');
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return [];
    }
}
