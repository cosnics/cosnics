<?php
namespace Chamilo\Libraries\Storage\Exception\Database;

use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Exception;

/**
 * @package Chamilo\Libraries\Storage\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DatabaseRetrieveException extends Exception
{

    protected string $dataClassName;

    protected DataClassRetrieveParameters $parameters;

    public function __construct(
        string $dataClassName, DataClassRetrieveParameters $parameters, string $exceptionMessage = ''
    )
    {
        $this->dataClassName = $dataClassName;
        $this->parameters = $parameters;

        parent::__construct(
            'Retrieve for ' . $dataClassName . ' with parameters ' . serialize($parameters) .
            ', failed with the following message:' . $exceptionMessage
        );
    }
}