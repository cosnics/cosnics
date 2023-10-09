<?php
namespace Chamilo\Core\User\Domain\UserImporter;

use RuntimeException;

/**
 * Describes the result of the importer action
 *
 * @package Chamilo\Core\User\Domain\UserImporter
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ImporterResult
{
    /**
     * @var \Chamilo\Core\User\Domain\UserImporter\ImportDataResult[]
     */
    protected array $failedUserResults;

    protected string $rawImportDataFooter;

    protected string $rawImportDataHeader;

    /**
     * @var \Chamilo\Core\User\Domain\UserImporter\ImportDataResult[]
     */
    protected array $successUserResults;

    public function __construct()
    {
        $this->failedUserResults = $this->successUserResults = [];
    }

    public function addFailedImportDataResult(ImportDataResult $importDataResult): void
    {
        if (!$importDataResult->hasFailed())
        {
            throw new RuntimeException('The import result could not be added because the import did not fail');
        }

        $this->failedUserResults[] = $importDataResult;
    }

    public function addImportDataResult(ImportDataResult $importDataResult): void
    {
        if (!$importDataResult->isCompleted())
        {
            throw new RuntimeException('The import result could not be added because the import is not yet completed');
        }

        if ($importDataResult->isSuccessful())
        {
            $this->addSuccessImportDataResult($importDataResult);
        }

        if ($importDataResult->hasFailed())
        {
            $this->addFailedImportDataResult($importDataResult);
        }
    }

    public function addSuccessImportDataResult(ImportDataResult $importDataResult): void
    {
        if (!$importDataResult->isSuccessful())
        {
            throw new RuntimeException(
                'The import result could not be added because the import was not successful'
            );
        }

        $this->successUserResults[] = $importDataResult;
    }

    public function countFailedUserResults(): int
    {
        return count($this->failedUserResults);
    }

    public function countResults(): int
    {
        return $this->countSuccessUserResults() + $this->countFailedUserResults();
    }

    public function countSuccessUserResults(): int
    {
        return count($this->successUserResults);
    }

    /**
     * @return ImportDataResult[]
     */
    public function getFailedImportDataResults(): array
    {
        return $this->failedUserResults;
    }

    public function getRawImportDataFooter(): string
    {
        return $this->rawImportDataFooter;
    }

    public function getRawImportDataHeader(): string
    {
        return $this->rawImportDataHeader;
    }

    /**
     * @return ImportDataResult[]
     */
    public function getSuccessImportDataResults(): array
    {
        return $this->successUserResults;
    }

    public function setRawImportDataFooter(string $rawImportDataFooter): ImporterResult
    {
        $this->rawImportDataFooter = $rawImportDataFooter;

        return $this;
    }

    public function setRawImportDataHeader(string $rawImportDataHeader): ImporterResult
    {
        $this->rawImportDataHeader = $rawImportDataHeader;

        return $this;
    }
}