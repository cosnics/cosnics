<?php
namespace Chamilo\Libraries\Storage\Exception\Database;

use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Exception;

/**
 * @package Chamilo\Libraries\Storage\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DatabaseCountGroupedException extends Exception
{
    protected string $dataClassStorageUnitName;

    protected DataClassCountGroupedParameters $parameters;

    public function __construct(
        string $dataClassStorageUnitName, DataClassCountGroupedParameters $parameters, string $exceptionMessage = ''
    )
    {
        $this->dataClassStorageUnitName = $dataClassStorageUnitName;
        $this->parameters = $parameters;

        parent::__construct(
            'Count Grouped for ' . $dataClassStorageUnitName . ' with parameters ' . serialize($parameters) .
            ', failed with the following message:' . $exceptionMessage
        );
    }
}