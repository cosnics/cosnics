<?php
namespace Chamilo\Libraries\Storage\Architecture\Exception;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Storage\Service\StorageMethodExceptionRenderer;
use Exception;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StorageMethodException extends Exception implements UserExceptionInterface
{
    protected string $dataClassStorageUnitName;

    protected ?string $exceptionMessage;

    protected string $method;

    protected ?string $query;

    public function __construct(
        string $method, string $dataClassStorageUnitName, ?string $exceptionMessage = null, ?string $query = null,
        ?string $message = null, int $code = 0, ?Exception $previousException = null
    )
    {
        parent::__construct($message, $code, $previousException);

        $this->method = $method;
        $this->dataClassStorageUnitName = $dataClassStorageUnitName;
        $this->query = $query;
        $this->exceptionMessage = $exceptionMessage;
    }

    public function getDataClassStorageUnitName(): string
    {
        return $this->dataClassStorageUnitName;
    }

    public function getExceptionMessage(): ?string
    {
        return $this->exceptionMessage;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function getUserExceptionRendererClassName(): string
    {
        return StorageMethodExceptionRenderer::class;
    }
}