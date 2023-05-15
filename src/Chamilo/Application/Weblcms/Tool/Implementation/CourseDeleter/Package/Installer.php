<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseDeleter\Package;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseDeleter\Manager;
use Chamilo\Application\Weblcms\Tool\ToolInstaller;

/**
 * Installs the tool data tables, settings, tracking, reporting
 *
 * @package application\weblcms\tool\course_deleter
 * @author  Mattias De Pauw - Hogeschool Gent
 * @author  Maarten Volckaert - Hogeschool Gent
 */
class Installer extends ToolInstaller
{
    public const CONTEXT = Manager::CONTEXT;
}
