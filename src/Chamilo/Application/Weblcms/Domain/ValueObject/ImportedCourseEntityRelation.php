<?php

namespace Chamilo\Application\Weblcms\Domain\ValueObject;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;

/**
 * Value object to define an imported course entity relation
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class ImportedCourseEntityRelation
{
    /**
     * @var int
     */
    protected $action;

    /**
     * @var string
     */
    protected $courseCode;

    /**
     * @var string
     */
    protected $entityStatus;

    /**
     * @param string $action
     * @param string $courseCode
     * @param string $entityStatus
     */
    public function __construct($action, $courseCode, $entityStatus)
    {
        $this->validateAction($action);
        $this->validateEntityStatus($entityStatus);

        $this->action = $action;
        $this->courseCode = $courseCode;
        $this->entityStatus = $entityStatus;
    }

    /**
     * @return int
     */
    public function getAction()
    {
        return $this->action;
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
     * Returns whether or not the imported course entity relation is due to be created
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->action == 'A';
    }

    /**
     * Returns whether or not the imported course entity relation is due to be updated
     *
     * @return bool
     */
    public function isUpdate()
    {
        return $this->action == 'U';
    }

    /**
     * Returns whether or not the imported course entity relation is due to be removed
     *
     * @return bool
     */
    public function isRemove()
    {
        return $this->action == 'D';
    }

    /**
     * Returns the CourseEntityRelation status value based on the entity status
     *
     * @return int
     */
    public function getStatusInteger()
    {
        return strtolower($this->getEntityStatus()) == 'teacher' ?
            CourseEntityRelation::STATUS_TEACHER : CourseEntityRelation::STATUS_STUDENT;
    }

    /**
     * Validates the action
     *
     * @param string $action
     *
     * @throws \Exception
     */
    protected function validateAction($action)
    {
        $allowedActions = array('A', 'U', 'D');
        if(!in_array(strtoupper($action), $allowedActions))
        {
            throw new \Exception(sprintf('The given action %s is invalid', $action));
        }
    }

    /**
     * Validates the status
     *
     * @param string $entityStatus
     *
     * @throws \Exception
     */
    protected function validateEntityStatus($entityStatus)
    {
        $allowedStatuses = array('teacher', 'student');
        if(!in_array(strtolower($entityStatus), $allowedStatuses))
        {
            throw new \Exception(sprintf('The given status %s is invalid', $entityStatus));
        }
    }

}