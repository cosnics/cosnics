<?php
namespace Chamilo\Libraries\Storage\Architecture\Exception;

use Chamilo\Libraries\Architecture\Exception\UserException;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StorageNoResultException extends UserException
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
            $method . ' for ' . $dataClassStorageUnitName . ' failed. ' . $exceptionMessage
        );
    }
}