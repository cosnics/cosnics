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

    protected string $dataClassStorageUnitName;

    protected DataClassRetrievesParameters $parameters;

    public function __construct(
        string $dataClassStorageUnitName, DataClassRetrievesParameters $parameters, string $exceptionMessage = ''
    )
    {
        $this->dataClassStorageUnitName = $dataClassStorageUnitName;
        $this->parameters = $parameters;

        parent::__construct(
            'Retrieves for ' . $dataClassStorageUnitName . ' with parameters ' . serialize($parameters) .
            ', failed with the following message:' . $exceptionMessage
        );
    }
}