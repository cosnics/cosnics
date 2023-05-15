<?php
namespace Chamilo\Application\Calendar\Package;

use Chamilo\Application\Calendar\Manager;

/**
 * @package application\calendar
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;

    public static function get_additional_packages()
    {
        return ['Chamilo\Application\Calendar\Extension\Personal'];
    }
}
