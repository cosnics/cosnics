<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Package;

use Chamilo\Application\Weblcms\Course\OpenCourse\Manager;

/**
 * @package application.lib.weblcms.install
 */

/**
 * This installer can be used to create the storage structure for the weblcms application.
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;
}
