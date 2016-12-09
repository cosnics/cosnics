<?php
namespace Chamilo\Application\Calendar\Package;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    public static function get_additional_packages()
    {
        return array('Chamilo\Application\Calendar\Extension\Personal');
    }
}
