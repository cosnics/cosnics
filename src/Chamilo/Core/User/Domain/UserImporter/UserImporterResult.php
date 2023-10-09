<?php
namespace Chamilo\Core\User\Domain\UserImporter;

/**
 * Describes the result of a single import action
 *
 * @package Chamilo\Core\User\Domain\UserImporter
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class UserImporterResult extends ImporterResult
{
    /**
     * @return \Chamilo\Core\User\Domain\UserImporter\ImportUserResult[]
     */
    public function getFailedUserResults(): array
    {
        return $this->failedUserResults;
    }

    /**
     * @return \Chamilo\Core\User\Domain\UserImporter\ImportUserResult[]
     */
    public function getSuccessUserResults(): array
    {
        return $this->successUserResults;
    }

}