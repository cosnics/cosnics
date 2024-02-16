<?php
namespace Chamilo\Libraries\Storage\Exception\Database;

use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Exception;

/**
 * @package Chamilo\Libraries\Storage\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DatabaseRetrievesException extends Exception
{

    protected string $dataClassName;

    protected DataClassRetrievesParameters $parameters;

    public function __construct(
        string $dataClassName, DataClassRetrievesParameters $parameters, string $exceptionMessage = ''
    )
    {
        $this->dataClassName = $dataClassName;
        $this->parameters = $parameters;

        parent::__construct(
            'Retrieves for ' . $dataClassName . ' with parameters ' . serialize($parameters) .
            ', failed with the following message:' . $exceptionMessage
        );
    }
}