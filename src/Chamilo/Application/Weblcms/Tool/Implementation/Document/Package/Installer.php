<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Document\Package;

use Chamilo\Application\Weblcms\Tool\Implementation\Document\Manager;
use Chamilo\Application\Weblcms\Tool\ToolInstaller;

/**
 * Installs the tool data tables, settings, tracking, reporting
 * 
 * @package application\weblcms\tool\document
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Installer extends ToolInstaller
{
    public const CONTEXT = Manager::CONTEXT;
}
