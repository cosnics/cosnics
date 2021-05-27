<?php
namespace Chamilo\Libraries\Package;

/**
 *
 * @package Chamilo\Libraries\Package
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    /**
     * Returns the list with extra installable packages that are connected to this package
     *
     * @return string[]
     */
    public static function get_additional_packages()
    {
        $packages = [];
        $packages[] = 'Chamilo\Libraries\Calendar';
        $packages[] = 'Chamilo\Libraries\Rights';
        return $packages;
    }
}
