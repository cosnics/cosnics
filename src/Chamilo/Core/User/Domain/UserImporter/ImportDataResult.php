<?php
namespace Chamilo\Core\User\Domain\UserImporter;

/**
 * Describes the result of a single import action
 *
 * @package Chamilo\Core\User\Domain\UserImporter
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class ImportDataResult
{
    public const STATUS_FAILED = 2;

    public const STATUS_SUCCESS = 1;

    protected ImportData $importData;

    /**
     * Detailed logging / debugging messages.
     *
     * @var string[]
     */
    protected array $messages;

    protected int $status;

    public function __construct(ImportData $importData)
    {
        $importData->setImportDataResult($this);

        $this->importData = $importData;
        $this->messages = [];
    }

    public function addMessage(string $message): void
    {
        $this->messages[] = $message;
    }

    public function getImportData(): ImportData
    {
        return $this->importData;
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function hasFailed(): bool
    {
        return $this->getStatus() == self::STATUS_FAILED;
    }

    public function isCompleted(): bool
    {
        return $this->hasFailed() || $this->isSuccessful();
    }

    public function isSuccessful(): bool
    {
        return $this->getStatus() == self::STATUS_SUCCESS;
    }

    public function setFailed(): void
    {
        $this->setStatus(self::STATUS_FAILED);
    }

    public function setImportData(ImportData $importData): static
    {
        $this->importData = $importData;

        return $this;
    }

    /**
     * @param string[] $messages
     */
    public function setMessages(array $messages): static
    {
        $this->messages = $messages;

        return $this;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function setSuccessful(): void
    {
        $this->setStatus(self::STATUS_SUCCESS);
    }

}