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

    protected string $dataClassStorageUnitName;

    protected DataClassRetrieveParameters $parameters;

    public function __construct(
        string $dataClassStorageUnitName, DataClassRetrieveParameters $parameters, string $exceptionMessage = ''
    )
    {
        $this->dataClassStorageUnitName = $dataClassStorageUnitName;
        $this->parameters = $parameters;

        parent::__construct(
            'Retrieve for ' . $dataClassStorageUnitName . ' with parameters ' . serialize($parameters) .
            ', failed with the following message:' . $exceptionMessage
        );
    }
}