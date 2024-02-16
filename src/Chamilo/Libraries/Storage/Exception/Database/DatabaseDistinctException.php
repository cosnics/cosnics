<?php
namespace Chamilo\Libraries\Storage\Exception\Database;

use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Exception;

/**
 * @package Chamilo\Libraries\Storage\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DatabaseDistinctException extends Exception
{
    protected string $dataClassName;

    protected DataClassDistinctParameters $parameters;

    public function __construct(
        string $dataClassName, DataClassDistinctParameters $parameters, string $exceptionMessage = ''
    )
    {
        $this->dataClassName = $dataClassName;
        $this->parameters = $parameters;

        parent::__construct(
            'Distinct for ' . $dataClassName . ' with parameters ' . serialize($parameters) .
            ', failed with the following message:' . $exceptionMessage
        );
    }
}