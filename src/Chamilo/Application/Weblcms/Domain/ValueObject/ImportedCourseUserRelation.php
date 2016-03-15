<?php

namespace Chamilo\Application\Weblcms\Domain\ValueObject;

/**
 * Value object to define an imported course user relation
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImportedCourseUserRelation extends ImportedCourseEntityRelation
{
    /**
     * @var string
     */
    protected $username;

    /**
     * @param string $action
     * @param string $courseCode
     * @param string $entityStatus
     * @param string $username
     */
    public function __construct($action, $courseCode, $entityStatus, $username)
    {
        parent::__construct($action, $courseCode, $entityStatus);

        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
}