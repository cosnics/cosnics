<?php
namespace Chamilo\Libraries\Authentication\Cas\Package;

/**
 *
 * @package Chamilo\Libraries\Authentication\Cas\Package
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
        $packages = array();
        $packages[] = 'Chamilo\Libraries\Calendar';
        return $packages;
    }
}
