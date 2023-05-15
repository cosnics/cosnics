<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Package;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\ToolInstaller;

/**
 * Installs the tool data tables, settings, tracking, reporting
 *
 * @package application\weblcms\tool\course_group
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class Installer extends ToolInstaller
{
    public const CONTEXT = Manager::CONTEXT;
}
