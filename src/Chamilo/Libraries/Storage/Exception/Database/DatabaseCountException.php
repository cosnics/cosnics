<?php
namespace Chamilo\Libraries\Storage\Exception\Database;

use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Exception;

/**
 * @package Chamilo\Libraries\Storage\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DatabaseCountException extends Exception
{
    protected string $dataClassName;

    protected DataClassCountParameters $parameters;

    public function __construct(
        string $dataClassName, DataClassCountParameters $parameters, string $exceptionMessage = ''
    )
    {
        $this->dataClassName = $dataClassName;
        $this->parameters = $parameters;

        parent::__construct(
            'Count for ' . $dataClassName . ' with parameters ' . serialize($parameters) .
            ', failed with the following message:' . $exceptionMessage
        );
    }
}