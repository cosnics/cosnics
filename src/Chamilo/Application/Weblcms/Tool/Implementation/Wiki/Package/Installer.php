<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Wiki\Package;

use Chamilo\Application\Weblcms\Tool\Implementation\Wiki\Manager;
use Chamilo\Application\Weblcms\Tool\ToolInstaller;

/**
 * Installs the tool data tables, settings, tracking, reporting
 * 
 * @package application\weblcms\tool\wiki
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Installer extends ToolInstaller
{
    public const CONTEXT = Manager::CONTEXT;
}
