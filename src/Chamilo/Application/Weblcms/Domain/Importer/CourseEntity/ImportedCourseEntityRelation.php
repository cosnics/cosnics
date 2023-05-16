<?php
namespace Chamilo\Application\Weblcms\Domain\Importer\CourseEntity;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\User\Domain\UserImporter\ImportData;

/**
 * Value object to define an imported course entity relation
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class ImportedCourseEntityRelation extends ImportData
{
    /**
     * @var string
     */
    protected $courseCode;

    /**
     * @var string
     */
    protected $entityStatus;

    /**
     * @param string $rawImportData
     * @param string $action
     * @param string $courseCode
     * @param string $entityStatus
     */
    public function __construct($rawImportData, $action, $courseCode, $entityStatus)
    {
        parent::__construct($rawImportData, $action);

        $this->courseCode = $courseCode;
        $this->entityStatus = $entityStatus;
    }

    /**
     * @return string
     */
    public function getCourseCode()
    {
        return $this->courseCode;
    }

    /**
     * @return string
     */
    public function getEntityStatus()
    {
        return $this->entityStatus;
    }

    /**
     * Returns the CourseEntityRelation status value based on the entity status
     *
     * @return int
     */
    public function getStatusInteger()
    {
        return strtolower($this->getEntityStatus()) == 'teacher' ? CourseEntityRelation::STATUS_TEACHER :
            CourseEntityRelation::STATUS_STUDENT;
    }

    /**
     * Checks whether or not this imported course entity relation has a valid status
     *
     * @param string $entityStatus
     *
     * @return bool
     */
    public function hasValidStatus($entityStatus)
    {
        $allowedStatuses = array('teacher', 'student');
        return in_array(strtolower($entityStatus), $allowedStatuses);
    }

    /**
     * Returns the list of valid actions
     *
     * @return array
     */
    public function getValidActions()
    {
        return [self::ACTION_ADD, self::ACTION_UPDATE, self::ACTION_DELETE];
    }
}