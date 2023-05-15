<?php
namespace Chamilo\Core\Notification\Package;

use Chamilo\Configuration\Package\Action\DoctrineInstaller;
use Chamilo\Core\Notification\Manager;

/**
 * @package Chamilo\Core\Notification\Package
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class Installer extends DoctrineInstaller
{
    public const CONTEXT = Manager::CONTEXT;
}
