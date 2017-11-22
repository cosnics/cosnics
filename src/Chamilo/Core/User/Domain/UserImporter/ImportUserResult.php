<?php

namespace Chamilo\Core\User\Domain\UserImporter;

/**
 * Describes the result of a single import action
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImportUserResult extends ImportDataResult
{

    /**
     * @return ImportUserData | \Chamilo\Core\User\Domain\UserImporter\ImportData
     */
    public function getImportUserData(): ImportUserData
    {
        return $this->importData;
    }

    /**
     * @param ImportUserData $importUserData
     *
     * @return ImportUserResult
     */
    public function setImportUserData(ImportUserData $importUserData): ImportUserResult
    {
        $this->importData = $importUserData;

        return $this;
    }
}