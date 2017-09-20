<?php

namespace Chamilo\Core\User\Domain\UserImporter;

/**
 * Describes the result of a single import action
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserImporterResult
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
     * List of import user results that have failed
     *
     * @var ImportUserResult[]
     */
    protected $failedUserResults;

    /**
     * List of import user results that are successful
     *
     * @var ImportUserResult[]
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
     * @param ImportUserResult $importUserResult
     */
    public function addFailedUserResult(ImportUserResult $importUserResult)
    {
        if(!$importUserResult->hasFailed())
        {
            throw new \RuntimeException('The import result could not be added because the user import did not fail');
        }

        $this->failedUserResults[] = $importUserResult;
    }

    /**
     * @param ImportUserResult $importUserResult
     */
    public function addSuccessUserResult(ImportUserResult $importUserResult)
    {
        if(!$importUserResult->isSuccessful())
        {
            throw new \RuntimeException(
                'The import result could not be added because the user import was not successful'
            );
        }

        $this->successUserResults[] = $importUserResult;
    }

    /**
     * Adds the result of a single user import.
     *
     * @param ImportUserResult $importUserResult
     *
     * @throws \Exception
     */
    public function addImportUserResult(ImportUserResult $importUserResult)
    {
        if (!$importUserResult->isCompleted())
        {
            throw new \RuntimeException('The import result could not be added because the user import is not yet completed');
        }

        if ($importUserResult->isSuccessful())
        {
            $this->addSuccessUserResult($importUserResult);
        }

        if ($importUserResult->hasFailed())
        {
            $this->addFailedUserResult($importUserResult);
        }
    }

    /**
     * @return ImportUserResult[]
     */
    public function getFailedUserResults(): array
    {
        return $this->failedUserResults;
    }

    /**
     * @return ImportUserResult[]
     */
    public function getSuccessUserResults(): array
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
     * @return UserImporterResult
     */
    public function setRawImportDataHeader($rawImportDataHeader): UserImporterResult
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
     * @return UserImporterResult
     */
    public function setRawImportDataFooter($rawImportDataFooter): UserImporterResult
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
     * Counts the successful user results
     *
     * @return int
     */
    public function countSuccessUserResults()
    {
        return count($this->successUserResults);
    }

    /**
     * Counts the failed user results
     *
     * @return int
     */
    public function countFailedUserResults()
    {
        return count($this->failedUserResults);
    }
}