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
    public const ACTION_DELETE_COURSE = 'CourseDeleter';

    public const CONTEXT = __NAMESPACE__;

    public const DEFAULT_ACTION = self::ACTION_DELETE_COURSE;
}
