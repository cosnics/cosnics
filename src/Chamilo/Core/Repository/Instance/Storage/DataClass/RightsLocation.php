<?php
namespace Chamilo\Core\Repository\Instance\Storage\DataClass;

use Chamilo\Core\Repository\Instance\Storage\DataManager;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsLocation extends \Chamilo\Core\Rights\RightsLocation
{

    /**
     *
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_instance_rights_location';
    }
}
