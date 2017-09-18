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
        $this->failedUserResults[] = $importUserResult;
    }

    /**
     * @param ImportUserResult $importUserResult
     */
    public function addSuccessUserResult(ImportUserResult $importUserResult)
    {
        $this->successUserResults[] = $importUserResult;
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
}