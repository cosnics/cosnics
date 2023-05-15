<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Package;

use Chamilo\Application\Calendar\Extension\Personal\Manager;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;
}
