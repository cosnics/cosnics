<?php
namespace Chamilo\Libraries\Storage\Architecture\Exception;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Service\StorageNoResultExceptionRenderer;
use Exception;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StorageNoResultException extends Exception implements UserExceptionInterface
{
    protected string $dataClassStorageUnitName;

    protected string $query;

    protected StorageParameters $storageParameters;

    public function __construct(
        string $dataClassStorageUnitName, StorageParameters $parameters, string $query, ?string $message = null,
        int $code = 0, ?Exception $previousException = null
    )
    {
        parent::__construct($message, $code, $previousException);

        $this->dataClassStorageUnitName = $dataClassStorageUnitName;
        $this->storageParameters = $parameters;
        $this->query = $query;
    }

    public function getDataClassStorageUnitName(): string
    {
        return $this->dataClassStorageUnitName;
    }

    public function setDataClassStorageUnitName(string $dataClassStorageUnitName): StorageNoResultException
    {
        $this->dataClassStorageUnitName = $dataClassStorageUnitName;

        return $this;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function setQuery(string $query): StorageNoResultException
    {
        $this->query = $query;

        return $this;
    }

    public function getStorageParameters(): StorageParameters
    {
        return $this->storageParameters;
    }

    public function setStorageParameters(StorageParameters $storageParameters): StorageNoResultException
    {
        $this->storageParameters = $storageParameters;

        return $this;
    }

    public function getUserExceptionRendererClassName(): string
    {
        return StorageNoResultExceptionRenderer::class;
    }
}