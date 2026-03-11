<?php
namespace Chamilo\Libraries\Storage\Architecture\Exception;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Storage\Service\StorageLastInsertedIdentifierExceptionRenderer;
use Exception;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StorageLastInsertedIdentifierException extends Exception implements UserExceptionInterface
{
    protected string $dataClassStorageUnitName;

    protected ?string $exceptionMessage;

    public function __construct(
        string $dataClassStorageUnitName, ?string $exceptionMessage, ?string $message = null, int $code = 0,
        ?Exception $previousException = null
    )
    {
        parent::__construct($message, $code, $previousException);

        $this->dataClassStorageUnitName = $dataClassStorageUnitName;
        $this->exceptionMessage = $exceptionMessage;
    }

    public function getDataClassStorageUnitName(): string
    {
        return $this->dataClassStorageUnitName;
    }

    public function setDataClassStorageUnitName(string $dataClassStorageUnitName
    ): StorageLastInsertedIdentifierException
    {
        $this->dataClassStorageUnitName = $dataClassStorageUnitName;

        return $this;
    }

    public function getExceptionMessage(): ?string
    {
        return $this->exceptionMessage;
    }

    public function setExceptionMessage(?string $exceptionMessage): StorageLastInsertedIdentifierException
    {
        $this->exceptionMessage = $exceptionMessage;

        return $this;
    }

    public function getUserExceptionRendererClassName(): string
    {
        return StorageLastInsertedIdentifierExceptionRenderer::class;
    }
}