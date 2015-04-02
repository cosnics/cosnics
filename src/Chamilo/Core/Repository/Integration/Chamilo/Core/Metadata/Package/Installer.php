<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Package;

/**
 * Installer for this package
 * 
 * @package repository\integration\core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    public static function get_additional_packages()
    {
        $installers = array();
        $installers[] = 'Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Type';
        $installers[] = 'Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property';
        $installers[] = 'Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Alternative';
        
        return $installers;
    }
}
