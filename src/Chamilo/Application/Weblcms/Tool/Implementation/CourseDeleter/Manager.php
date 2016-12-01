<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseDeleter;

/**
 * This tool is used for deleting a course completly
 * 
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Maarten Volckaert - Hogeschool Gent
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    const ACTION_DELETE_COURSE = 'CourseDeleter';
    const DEFAULT_ACTION = self::ACTION_DELETE_COURSE;
}
