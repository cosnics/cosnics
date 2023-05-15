<?php
namespace Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Core\Menu\Manager;

/**
 * @package Chamilo\Core\Menu\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class RightsLocation extends \Chamilo\Libraries\Rights\Domain\RightsLocation
{
    public const CONTEXT = Manager::CONTEXT;

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'menu_rights_location';
    }
}
