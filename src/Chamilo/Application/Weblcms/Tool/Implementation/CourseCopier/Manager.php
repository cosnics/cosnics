<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier;

/**
 * This tool implements the course emptier tool for a course.
 * 
 * @author Mattias De Pauw - Hogeschool Gent
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    const DEFAULT_ACTION = self::ACTION_BROWSE;
    const ACTION_BROWSE = 'Browser';
}
