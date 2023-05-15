<?php
namespace Chamilo\Core\Repository\Workspace\Extension\Office365\Package;

use Chamilo\Core\Repository\Workspace\Extension\Office365\Manager;

/**
 * @package Chamilo\Core\Repository\Workspace\Extension\Office365\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;
}
