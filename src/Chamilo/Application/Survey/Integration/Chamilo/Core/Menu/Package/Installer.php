<?php
namespace Chamilo\Application\Survey\Integration\Chamilo\Core\Menu\Package;

use Chamilo\Core\Menu\Storage\DataClass\Item;

class Installer extends \Chamilo\Core\Menu\Action\Installer
{

    /**
     *
     * @param string[] $formValues
     */
    public function __construct($formValues)
    {
        parent :: __construct($formValues, Item :: DISPLAY_ICON, false);
    }
}
