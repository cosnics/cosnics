<?php
namespace Chamilo\Core\User\Domain\UserImporter;

/**
 * Describes the data for a single import
 *
 * @package Chamilo\Core\User\Domain\UserImporter
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class ImportData
{
    public const ACTION_ADD = 'A';

    public const ACTION_ADD_UPDATE = 'UA';

    public const ACTION_DELETE = 'D';

    public const ACTION_UPDATE = 'U';

    protected string $action;

    protected ImportDataResult $importDataResult;

    /**
     * The imported data as a raw string. It is used to give the user the opportunity to retry the failed imports.
     */
    protected string $rawImportData;

    /**
     * ImportData constructor.
     *
     * @param string $rawImportData
     * @param string $action
     */
    public function __construct(string $rawImportData, string $action)
    {
        $this->rawImportData = $rawImportData;
        $this->action = $action;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getImportDataResult(): ImportDataResult
    {
        return $this->importDataResult;
    }

    public function getRawImportData(): string
    {
        return $this->rawImportData;
    }

    abstract public function getValidActions(): array;

    public function hasValidAction(): bool
    {
        return in_array($this->getAction(), $this->getValidActions());
    }

    public function isDelete(): bool
    {
        return $this->getAction() == self::ACTION_DELETE;
    }

    public function isNew(): bool
    {
        return $this->getAction() == self::ACTION_ADD;
    }

    public function isNewOrUpdate(): bool
    {
        return $this->getAction() == self::ACTION_ADD_UPDATE;
    }

    public function isUpdate(): bool
    {
        return $this->getAction() == self::ACTION_UPDATE;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function setActionToNew(): void
    {
        $this->setAction(self::ACTION_ADD);
    }

    public function setActionToUpdate(): void
    {
        $this->setAction(self::ACTION_UPDATE);
    }

    public function setImportDataResult(ImportDataResult $importDataResult): static
    {
        $this->importDataResult = $importDataResult;

        return $this;
    }

    public function setRawImportData(string $rawImportData): static
    {
        $this->rawImportData = $rawImportData;

        return $this;
    }
}