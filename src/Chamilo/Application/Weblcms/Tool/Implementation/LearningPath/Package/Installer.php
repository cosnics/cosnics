<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Package;

use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Application\Weblcms\Tool\ToolInstaller;

/**
 * Installs the tool data tables, settings, tracking, reporting
 *
 * @package application\weblcms\tool\learning_path
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class Installer extends ToolInstaller
{
    public const CONTEXT = Manager::CONTEXT;
}
