<?php
namespace Chamilo\Libraries\Storage\Exception;

use Chamilo\Libraries\Storage\Query\DataClassParameters;
use Exception;

/**
 * @package Chamilo\Libraries\Storage\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StorageNoResultException extends Exception
{
    protected string $dataClassStorageUnitName;

    protected DataClassParameters $parameters;

    protected string $query;

    public function __construct(
        string $method, string $dataClassStorageUnitName, DataClassParameters $parameters, string $exceptionMessage = ''
    )
    {
        $this->dataClassStorageUnitName = $dataClassStorageUnitName;
        $this->parameters = $parameters;

        parent::__construct(
            $method . ' for ' . $dataClassStorageUnitName . ' failed with the following message:' . $exceptionMessage
        );
    }
}