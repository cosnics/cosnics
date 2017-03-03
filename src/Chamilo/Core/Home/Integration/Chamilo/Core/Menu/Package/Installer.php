<?php
namespace Chamilo\Core\Home\Integration\Chamilo\Core\Menu\Package;

use Chamilo\Core\Menu\Storage\DataClass\Item;

/**
 *
 * @package Chamilo\Core\Home\Integration\Chamilo\Core\Menu\Package
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Core\Menu\Action\Installer
{

    /**
     *
     * @param string[] $formValues
     */
    public function __construct($formValues)
    {
        parent::__construct($formValues, Item::DISPLAY_BOTH, false);
    }
}
