<?php
namespace Chamilo\Application\Weblcms\Domain\Importer\CourseEntity;

/**
 * Value object to define an imported course group relation
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImportedCourseGroupRelation extends ImportedCourseEntityRelation
{
    /**
     * @var string
     */
    protected $groupCode;

    /**
     * @param string $rawImportData
     * @param string $action
     * @param string $courseCode
     * @param string $entityStatus
     * @param string $groupCode
     */
    public function __construct($rawImportData, $action, $courseCode, $entityStatus, $groupCode)
    {
        parent::__construct($rawImportData, $action, $courseCode, $entityStatus);
        
        $this->groupCode = $groupCode;
    }

    /**
     * @return string
     */
    public function getGroupCode()
    {
        return $this->groupCode;
    }
}