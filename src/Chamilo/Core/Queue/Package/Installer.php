<?php
namespace Chamilo\Core\Queue\Package;

use Chamilo\Configuration\Package\Action\DoctrineInstaller;
use Chamilo\Core\Queue\Manager;

/**
 * @package Chamilo\Core\Queue\Package
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class Installer extends DoctrineInstaller
{
    public const CONTEXT = Manager::CONTEXT;
}
