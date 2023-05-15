<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseTruncater\Package;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseTruncater\Manager;
use Chamilo\Application\Weblcms\Tool\ToolInstaller;

/**
 * Installs the tool data tables, settings, tracking, reporting
 * 
 * @package application\weblcms\tool\course_truncate
 * @author Maarten Volckaert - Hogeschool Gent
 */
class Installer extends ToolInstaller
{
    public const CONTEXT = Manager::CONTEXT;
}
