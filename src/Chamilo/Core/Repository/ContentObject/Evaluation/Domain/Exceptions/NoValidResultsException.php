<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions;

/**
 * Class NoValidResultsException
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class NoValidResultsException extends \Exception implements CuriosImportException
{
    /**
     * NoValidResultsException constructor.
     */
    public function __construct()
    {
        parent::__construct('The uploaded file does not contain any results that can be imported.');
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return [];
    }
}
