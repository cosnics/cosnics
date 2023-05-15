<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Forum\Package;

use Chamilo\Application\Weblcms\Tool\Implementation\Forum\Manager;
use Chamilo\Application\Weblcms\Tool\ToolInstaller;

/**
 * Installs the tool data tables, settings, tracking, reporting
 * 
 * @package application\weblcms\tool\forum
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Installer extends ToolInstaller
{
    public const CONTEXT = Manager::CONTEXT;
}
