<?php
namespace Chamilo\Core\User\Domain\UserImporter;

use RuntimeException;

/**
 * Describes the result of the importer action
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImporterResult
{
    /**
     * @var string
     */
    protected $rawImportDataHeader;

    /**
     * @var string
     */
    protected $rawImportDataFooter;

    /**
     * List of import data results that have failed
     *
     * @var ImportDataResult[]
     */
    protected $failedUserResults;

    /**
     * List of import data results that are successful
     *
     * @var ImportDataResult[]
     */
    protected $successUserResults;

    /**
     * UserImporterResult constructor.
     */
    public function __construct()
    {
        $this->failedUserResults = $this->successUserResults = [];
    }

    /**
     * @param ImportDataResult $importDataResult
     */
    public function addFailedImportDataResult(ImportDataResult $importDataResult)
    {
        if(!$importDataResult->hasFailed())
        {
            throw new RuntimeException('The import result could not be added because the import did not fail');
        }

        $this->failedUserResults[] = $importDataResult;
    }

    /**
     * @param ImportDataResult $importDataResult
     */
    public function addSuccessImportDataResult(ImportDataResult $importDataResult)
    {
        if(!$importDataResult->isSuccessful())
        {
            throw new RuntimeException(
                'The import result could not be added because the import was not successful'
            );
        }

        $this->successUserResults[] = $importDataResult;
    }

    /**
     * Adds the result of a single import.
     *
     * @param ImportDataResult $importDataResult
     *
     * @throws \Exception
     */
    public function addImportDataResult(ImportDataResult $importDataResult)
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

    /**
     * @return ImportDataResult[]
     */
    public function getFailedImportDataResults(): array
    {
        return $this->failedUserResults;
    }

    /**
     * @return ImportDataResult[]
     */
    public function getSuccessImportDataResults(): array
    {
        return $this->successUserResults;
    }

    /**
     * @return string
     */
    public function getRawImportDataHeader()
    {
        return $this->rawImportDataHeader;
    }

    /**
     * @param string $rawImportDataHeader
     *
     * @return $this
     */
    public function setRawImportDataHeader($rawImportDataHeader)
    {
        $this->rawImportDataHeader = $rawImportDataHeader;

        return $this;
    }

    /**
     * @return string
     */
    public function getRawImportDataFooter()
    {
        return $this->rawImportDataFooter;
    }

    /**
     * @param string $rawImportDataFooter
     *
     * @return $this
     */
    public function setRawImportDataFooter($rawImportDataFooter)
    {
        $this->rawImportDataFooter = $rawImportDataFooter;

        return $this;
    }

    /**
     * Counts the total amount of results
     *
     * @return int
     */
    public function countResults()
    {
        return $this->countSuccessUserResults() + $this->countFailedUserResults();
    }

    /**
     * Counts the successful results
     *
     * @return int
     */
    public function countSuccessUserResults()
    {
        return count($this->successUserResults);
    }

    /**
     * Counts the failed results
     *
     * @return int
     */
    public function countFailedUserResults()
    {
        return count($this->failedUserResults);
    }
}