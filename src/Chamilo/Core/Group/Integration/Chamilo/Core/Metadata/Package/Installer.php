<?php
namespace Chamilo\Core\Group\Integration\Chamilo\Core\Metadata\Package;

use Chamilo\Core\Group\Storage\DataManager;

/**
 * Installer for this package
 * 
 * @package group\integration\core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    public function __construct($form_values = null)
    {
        parent :: __construct($form_values, DataManager :: get_instance());
    }
}
