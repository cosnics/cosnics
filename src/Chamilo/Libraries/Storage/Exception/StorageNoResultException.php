<?php
namespace Chamilo\Libraries\Storage\Exception;

use Chamilo\Libraries\Storage\Query\StorageParameters;
use Exception;

/**
 * @package Chamilo\Libraries\Storage\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StorageNoResultException extends Exception
{
    protected string $dataClassStorageUnitName;

    protected StorageParameters $parameters;

    protected string $query;

    public function __construct(
        string $method, string $dataClassStorageUnitName, StorageParameters $parameters, string $exceptionMessage = ''
    )
    {
        $this->dataClassStorageUnitName = $dataClassStorageUnitName;
        $this->parameters = $parameters;

        parent::__construct(
            $method . ' for ' . $dataClassStorageUnitName . ' failed with the following message:' . $exceptionMessage
        );
    }
}