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
    protected string $dataClassStorageUnitName;

    protected DataClassDistinctParameters $parameters;

    public function __construct(
        string $dataClassStorageUnitName, DataClassDistinctParameters $parameters, string $exceptionMessage = ''
    )
    {
        $this->dataClassStorageUnitName = $dataClassStorageUnitName;
        $this->parameters = $parameters;

        parent::__construct(
            'Distinct for ' . $dataClassStorageUnitName . ' with parameters ' . serialize($parameters) .
            ', failed with the following message:' . $exceptionMessage
        );
    }
}