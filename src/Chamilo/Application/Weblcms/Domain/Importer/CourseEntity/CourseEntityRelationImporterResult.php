<?php
namespace Chamilo\Application\Weblcms\Domain\Importer\CourseEntity;

use Chamilo\Core\User\Domain\UserImporter\ImporterResult;

/**
 * Extension on the ImporterResult for CourseEntityRelation
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseEntityRelationImporterResult extends ImporterResult
{
    const IMPORT_TYPE_USER = 1;
    const IMPORT_TYPE_GROUP = 2;

    /**
     * @var int
     */
    protected $importType;

    /**
     * @return int
     */
    public function getImportType(): int
    {
        return $this->importType;
    }

    /**
     * @param int $importType
     *
     * @return $this
     */
    public function setImportType(int $importType)
    {
        $this->importType = $importType;

        return $this;
    }

    /**
     * Returns whether or not the import is for relations between users and courses
     *
     * @return bool
     */
    public function isUserRelationImport()
    {
        return $this->getImportType() == self::IMPORT_TYPE_USER;
    }

    /**
     * Sets the import type to user relation
     */
    public function setUserRelationImportType()
    {
        $this->setImportType(self::IMPORT_TYPE_USER);
    }

    /**
     * Returns whether or not the import is for relations between groups and courses
     *
     * @return bool
     */
    public function isGroupRelationImport()
    {
        return $this->getImportType() == self::IMPORT_TYPE_GROUP;
    }

    /**
     * Sets the import type to group relation
     */
    public function setGroupRelationImportType()
    {
        $this->setImportType(self::IMPORT_TYPE_GROUP);
    }
}