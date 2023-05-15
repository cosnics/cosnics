<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Package;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Manager;
use Chamilo\Application\Weblcms\Tool\ToolInstaller;

/**
 * Installs the tool data tables, settings, tracking, reporting
 *
 * @package application\weblcms\tool\course_emptier
 * @author  Maarten Volckaert - Hogeschool Gent
 */
class Installer extends ToolInstaller
{
    public const CONTEXT = Manager::CONTEXT;
}
