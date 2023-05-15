<?php
namespace Chamilo\Core\Rights\Package;

use Chamilo\Core\Rights\Manager;

/**
 * @package Chamilo\Core\Rights\Package
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;
}
