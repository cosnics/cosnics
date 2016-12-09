<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseTruncater;

/**
 * This tool implements the course truncate tool for a course.
 * 
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Maarten Volckaert - Hogeschool Gent
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    const ACTION_BROWSE = 'Browser';
    const DEFAULT_ACTION = self::ACTION_BROWSE;
}
