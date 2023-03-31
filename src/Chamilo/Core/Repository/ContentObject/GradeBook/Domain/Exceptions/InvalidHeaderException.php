<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions;

/**
 * Class InvalidHeaderException
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class InvalidHeaderException extends \Exception implements GradeBookImportException
{
    /**
     * InvalidHeaderException constructor.
     */
    public function __construct()
    {
        parent::__construct('The header is not in the format of `lastname;firstname;id;...`');
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return [];
    }
}
