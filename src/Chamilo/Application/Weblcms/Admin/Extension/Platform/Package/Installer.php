<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Package;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Manager;

/**
 * @package Chamilo\Application\Weblcms\Admin\Extension\Platform
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;
}
