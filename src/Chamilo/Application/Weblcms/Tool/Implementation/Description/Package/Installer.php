<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Description\Package;

use Chamilo\Application\Weblcms\Tool\Implementation\Description\Manager;
use Chamilo\Application\Weblcms\Tool\ToolInstaller;

/**
 * Installs the tool data tables, settings, tracking, reporting
 *
 * @package application\weblcms\tool\description
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class Installer extends ToolInstaller
{
    public const CONTEXT = Manager::CONTEXT;
}
