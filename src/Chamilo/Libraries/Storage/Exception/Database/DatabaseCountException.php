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
    protected string $dataClassStorageUnitName;

    protected DataClassCountParameters $parameters;

    public function __construct(
        string $dataClassStorageUnitName, DataClassCountParameters $parameters, string $exceptionMessage = ''
    )
    {
        $this->dataClassStorageUnitName = $dataClassStorageUnitName;
        $this->parameters = $parameters;

        parent::__construct(
            'Count for ' . $dataClassStorageUnitName . ' with parameters ' . serialize($parameters) .
            ', failed with the following message:' . $exceptionMessage
        );
    }
}