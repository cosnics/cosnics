<?php
namespace Chamilo\Core\User\Domain\UserImporter;

/**
 * Describes the result of a single import action
 *
 * @package Chamilo\Core\User\Domain\UserImporter
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class ImportUserResult extends ImportDataResult
{

    public function getImportUserData(): ImportData
    {
        return $this->importData;
    }

    public function setImportUserData(ImportUserData $importUserData): ImportUserResult
    {
        $this->importData = $importUserData;

        return $this;
    }
}