<?php
namespace Chamilo\Libraries\Storage\Exception\Database;

use Exception;

/**
 * @package Chamilo\Libraries\Storage\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DatabaseCreateException extends Exception
{

    protected string $dataClassName;

    protected array $record;

    public function __construct($dataClassName, array $record, string $exceptionMessage = '')
    {
        $this->dataClassName = $dataClassName;
        $this->record = $record;

        parent::__construct(
            'Create for ' . $dataClassName . ' failed with the following message:' . $exceptionMessage
        );
    }
}