<?php
namespace Chamilo\Application\Portfolio\Package;

use Chamilo\Application\Portfolio\Manager;

/**
 * @package Chamilo\Application\Portfolio\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;
}
